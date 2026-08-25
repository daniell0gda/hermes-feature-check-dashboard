<?php
/**
 * Markdown renderer for run documents.
 *
 * Deliberately a subset: headings, emphasis, code, links, images, lists,
 * blockquotes, tables and rules - which is everything the Hermes reports use.
 * Every span of text is escaped before any markup is added, so a report can
 * never inject HTML, and image references are resolved against the run's own
 * artifacts (an unknown reference renders as its alt text rather than a
 * hotlink or a broken image).
 */

if (!defined('ABSPATH')) {
    exit;
}

class HFCD_Markdown
{
    /** Body headings start below the document title in the page outline. */
    private const HEADING_OFFSET = 2;

    private const CODE_TOKEN = "\x00hfcd-code-%d\x00";

    /** @var callable|null */
    private $image_resolver;

    /** @var array<string,string> */
    private array $code_spans = [];

    private function __construct(?callable $image_resolver)
    {
        $this->image_resolver = $image_resolver;
    }

    /**
     * @param callable|null $image_resolver fn(string $reference): ?string
     */
    public static function to_html(string $markdown, ?callable $image_resolver = null): string
    {
        return (new self($image_resolver))->blocks(preg_split('/\R/', $markdown) ?: []);
    }

    /** @param string[] $lines */
    private function blocks(array $lines): string
    {
        $html = '';
        $index = 0;
        $count = count($lines);

        while ($index < $count) {
            $line = $lines[$index];

            if (trim($line) === '') {
                $index++;
                continue;
            }
            if (preg_match('/^\s*(```|~~~)\s*([A-Za-z0-9_+#.-]*)\s*$/', $line, $matches)) {
                $html .= $this->fenced_code($lines, $index, $matches[1]);
                continue;
            }
            if (preg_match('/^\s{0,3}(#{1,6})\s+(.*?)\s*#*\s*$/', $line, $matches)) {
                $level = min(6, strlen($matches[1]) + self::HEADING_OFFSET);
                $html .= '<h' . $level . '>' . $this->inline($matches[2]) . '</h' . $level . '>';
                $index++;
                continue;
            }
            if (preg_match('/^\s{0,3}([-*_])\s*(?:\1\s*){2,}$/', $line)) {
                $html .= '<hr>';
                $index++;
                continue;
            }
            if (preg_match('/^\s{0,3}>/', $line)) {
                $html .= $this->blockquote($lines, $index);
                continue;
            }
            if ($this->is_table_start($lines, $index)) {
                $html .= $this->table($lines, $index);
                continue;
            }
            if (preg_match('/^(\s*)([-*+]|\d+[.)])\s+/', $line)) {
                $html .= $this->list($lines, $index);
                continue;
            }

            $html .= $this->paragraph($lines, $index);
        }

        return $html;
    }

    /** @param string[] $lines */
    private function fenced_code(array $lines, int &$index, string $fence): string
    {
        $language = '';
        if (preg_match('/^\s*' . preg_quote($fence, '/') . '\s*([A-Za-z0-9_+#.-]*)/', $lines[$index], $matches)) {
            $language = strtolower($matches[1]);
        }
        $index++;

        $body = [];
        while ($index < count($lines) && !preg_match('/^\s*' . preg_quote($fence, '/') . '\s*$/', $lines[$index])) {
            $body[] = $lines[$index];
            $index++;
        }
        $index++; // closing fence

        $class = $language !== '' ? ' class="language-' . esc_attr($language) . '"' : '';

        return '<pre><code' . $class . '>' . esc_html(implode("\n", $body)) . '</code></pre>';
    }

    /** @param string[] $lines */
    private function blockquote(array $lines, int &$index): string
    {
        $body = [];
        while ($index < count($lines) && preg_match('/^\s{0,3}>\s?(.*)$/', $lines[$index], $matches)) {
            $body[] = $matches[1];
            $index++;
        }

        return '<blockquote>' . $this->blocks($body) . '</blockquote>';
    }

    /** @param string[] $lines */
    private function paragraph(array $lines, int &$index): string
    {
        $body = [];
        while ($index < count($lines)) {
            $line = $lines[$index];
            if (trim($line) === '' || $this->starts_block($lines, $index)) {
                break;
            }
            $body[] = trim($line);
            $index++;
        }

        return '<p>' . $this->inline(implode("\n", $body)) . '</p>';
    }

    /** @param string[] $lines */
    private function starts_block(array $lines, int $index): bool
    {
        $line = $lines[$index];

        return (bool) preg_match('/^\s*(```|~~~)/', $line)
            || (bool) preg_match('/^\s{0,3}#{1,6}\s+/', $line)
            || (bool) preg_match('/^\s{0,3}>/', $line)
            || (bool) preg_match('/^(\s*)([-*+]|\d+[.)])\s+/', $line)
            || $this->is_table_start($lines, $index);
    }

    /** @param string[] $lines */
    private function is_table_start(array $lines, int $index): bool
    {
        if (!str_contains($lines[$index], '|') || !isset($lines[$index + 1])) {
            return false;
        }

        return (bool) preg_match('/^\s*\|?[\s:-]*-[\s:|-]*\|[\s:|-]*$/', $lines[$index + 1]);
    }

    /** @param string[] $lines */
    private function table(array $lines, int &$index): string
    {
        $header = $this->table_cells($lines[$index]);
        $alignments = array_map(
            static function (string $spec): string {
                $spec = trim($spec);
                if (str_starts_with($spec, ':') && str_ends_with($spec, ':')) {
                    return 'center';
                }

                return str_ends_with($spec, ':') ? 'right' : '';
            },
            $this->table_cells($lines[$index + 1])
        );
        $index += 2;

        $html = '<div class="hfcd-table-scroll"><table><thead><tr>';
        foreach ($header as $position => $cell) {
            $html .= '<th' . $this->align_attribute($alignments[$position] ?? '') . '>' . $this->inline($cell) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        while ($index < count($lines) && str_contains($lines[$index], '|') && trim($lines[$index]) !== '') {
            $html .= '<tr>';
            foreach ($this->table_cells($lines[$index]) as $position => $cell) {
                $html .= '<td' . $this->align_attribute($alignments[$position] ?? '') . '>' . $this->inline($cell) . '</td>';
            }
            $html .= '</tr>';
            $index++;
        }

        return $html . '</tbody></table></div>';
    }

    /** @return string[] */
    private function table_cells(string $line): array
    {
        return array_map('trim', explode('|', trim(trim($line), '|')));
    }

    private function align_attribute(string $alignment): string
    {
        return $alignment === '' ? '' : ' style="text-align:' . $alignment . '"';
    }

    /**
     * Collect the whole list (including nested items) then build it, so
     * indentation maps to real nesting rather than sibling items.
     *
     * @param string[] $lines
     */
    private function list(array $lines, int &$index): string
    {
        $items = [];
        while ($index < count($lines)) {
            $line = $lines[$index];
            if (trim($line) === '') {
                // A blank line ends the list unless another item follows.
                if (!isset($lines[$index + 1]) || !preg_match('/^(\s*)([-*+]|\d+[.)])\s+/', $lines[$index + 1])) {
                    break;
                }
                $index++;
                continue;
            }

            if (preg_match('/^(\s*)([-*+]|\d+[.)])\s+(.*)$/', $line, $matches)) {
                $items[] = [
                    'level' => intdiv(strlen(str_replace("\t", '  ', $matches[1])), 2),
                    'ordered' => (bool) preg_match('/^\d/', $matches[2]),
                    'lines' => [$matches[3]],
                ];
                $index++;
                continue;
            }

            if ($items !== [] && preg_match('/^\s+\S/', $line)) {
                $items[count($items) - 1]['lines'][] = trim($line);
                $index++;
                continue;
            }

            break;
        }

        $position = 0;

        return $this->build_list($items, $position, $items ? $items[0]['level'] : 0);
    }

    /**
     * @param array<int,array{level:int,ordered:bool,lines:string[]}> $items
     */
    private function build_list(array $items, int &$position, int $level): string
    {
        $tag = ($items[$position]['ordered'] ?? false) ? 'ol' : 'ul';
        $html = '<' . $tag . '>';

        while ($position < count($items)) {
            $item = $items[$position];
            if ($item['level'] < $level) {
                break;
            }
            if ($item['level'] > $level) {
                $html .= $this->build_list($items, $position, $item['level']);
                continue;
            }

            $position++;
            $content = $this->inline(implode("\n", $item['lines']));
            $nested = '';
            if ($position < count($items) && $items[$position]['level'] > $level) {
                $nested = $this->build_list($items, $position, $items[$position]['level']);
            }
            $html .= '<li>' . $content . $nested . '</li>';
        }

        return $html . '</' . $tag . '>';
    }

    private function inline(string $text): string
    {
        $text = $this->protect_code_spans($text);
        $text = esc_html($text);

        $text = preg_replace_callback(
            '/!\[([^\]]*)\]\(\s*([^)\s]+)(?:\s+&quot;.*?&quot;)?\s*\)/',
            [$this, 'render_image'],
            $text
        ) ?? $text;
        $text = preg_replace_callback(
            '/\[([^\]]+)\]\(\s*([^)\s]+)(?:\s+&quot;.*?&quot;)?\s*\)/',
            [$this, 'render_link'],
            $text
        ) ?? $text;

        $text = preg_replace('/\*\*(?=\S)(.+?)(?<=\S)\*\*/s', '<strong>$1</strong>', $text) ?? $text;
        $text = preg_replace('/(?<![A-Za-z0-9_])__(?=\S)(.+?)(?<=\S)__(?![A-Za-z0-9_])/s', '<strong>$1</strong>', $text) ?? $text;
        $text = preg_replace('/(?<![A-Za-z0-9_*])\*(?=\S)([^*\n]+?)(?<=\S)\*(?![A-Za-z0-9_*])/', '<em>$1</em>', $text) ?? $text;
        $text = preg_replace('/(?<![A-Za-z0-9_])_(?=\S)([^_\n]+?)(?<=\S)_(?![A-Za-z0-9_])/', '<em>$1</em>', $text) ?? $text;
        $text = preg_replace('/~~(?=\S)(.+?)(?<=\S)~~/s', '<del>$1</del>', $text) ?? $text;

        $text = nl2br($text, false);

        return $this->restore_code_spans($text);
    }

    private function protect_code_spans(string $text): string
    {
        return preg_replace_callback(
            '/(`+)(.+?)\1/s',
            function (array $matches): string {
                $token = sprintf(self::CODE_TOKEN, count($this->code_spans));
                $this->code_spans[$token] = '<code>' . esc_html(trim($matches[2])) . '</code>';

                return $token;
            },
            $text
        ) ?? $text;
    }

    private function restore_code_spans(string $text): string
    {
        return $this->code_spans === [] ? $text : strtr($text, $this->code_spans);
    }

    private function render_image(array $matches): string
    {
        $alt = $matches[1];
        $source = $this->resolve_url($matches[2]);
        if ($source === null) {
            return '<span class="hfcd-missing-media" title="Artifact not published with this run">'
                . ($alt !== '' ? $alt : 'image') . '</span>';
        }

        return '<img src="' . esc_url($source) . '" alt="' . $alt . '" loading="lazy" decoding="async">';
    }

    private function render_link(array $matches): string
    {
        $label = $matches[1];
        $target = $this->decode_url($matches[2]);
        if (!preg_match('#^(https?:|mailto:|/|\#)#i', $target)) {
            $resolved = $this->resolve_url($matches[2]);
            if ($resolved === null) {
                return $label;
            }
            $target = $resolved;
        }

        $rel = preg_match('#^https?://#i', $target) ? ' rel="noopener noreferrer"' : '';

        return '<a href="' . esc_url($target) . '"' . $rel . '>' . $label . '</a>';
    }

    /** Absolute URLs pass through; relative ones must be known artifacts. */
    private function resolve_url(string $raw): ?string
    {
        $url = $this->decode_url($raw);
        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }
        if ($this->image_resolver === null) {
            return null;
        }

        $resolved = call_user_func($this->image_resolver, $url);

        return is_string($resolved) && $resolved !== '' ? $resolved : null;
    }

    private function decode_url(string $raw): string
    {
        return trim(wp_specialchars_decode($raw, ENT_QUOTES));
    }
}
