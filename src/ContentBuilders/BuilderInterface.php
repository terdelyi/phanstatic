<?php

namespace Terdelyi\Phanstatic\ContentBuilders;

interface BuilderInterface
{
    public function build(BuilderContextInterface $context): void;
}
