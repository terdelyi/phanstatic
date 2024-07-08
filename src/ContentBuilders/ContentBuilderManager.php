<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\ContentBuilders;

use Terdelyi\Phanstatic\Models\BuilderContextInterface;

class ContentBuilderManager
{
    public function __construct(
        private readonly BuilderContextInterface $context,
    ) {}

    /**
     * @param class-string<BuilderInterface>[] $builders
     */
    public function run(array $builders): void
    {
        /** @var class-string<BuilderInterface> $builderClassName */
        foreach ($builders as $builderClassName) {
            $this->create($builderClassName)
                ->build();
        }
    }

    /**
     * @param class-string<BuilderInterface> $builderClassName
     */
    public function create(string $builderClassName): BuilderInterface
    {
        return new $builderClassName($this->context);
    }
}
