<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Compilers;

use League\CommonMark\CommonMarkConverter;
use Spatie\YamlFrontMatter\Document;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class MarkdownCompiler
{
    private Document $frontMatter;
    private \SplFileInfo $file;

    public function render(string $path): self
    {
        $fileContent = file_get_contents($path);

        if ( ! $fileContent) {
            throw new \Exception('Markdown file is empty: '.$path);
        }

        $this->file = new \SplFileInfo($path);
        $this->frontMatter = YamlFrontMatter::parse($fileContent);

        return $this;
    }

    /** @return array<string, mixed> */
    public function meta(): array
    {
        $meta = $this->frontMatter->matter();

        // @TODO: Make it DateTime
        $meta['date'] ??= date(\DateTimeInterface::ATOM, $this->file->getMTime());

        return $meta;
    }

    public function content(): string
    {
        return (new CommonMarkConverter())
            ->convert($this->frontMatter->body())
            ->getContent();
    }
}
