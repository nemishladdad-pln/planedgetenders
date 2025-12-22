<?php
namespace App\DataTransferObjects;

use App\Enums;

class BuilderDto
{
    public function __construct(
        protected readonly public string $title
        ) {}
}
