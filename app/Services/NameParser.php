<?php

	namespace App\Services;
	use App\DTO\Person;

	class NameParser
	{
		public function parse(string $name): array
		{
			$name = trim($name);

			if ($name === '') {
				return [];
			}

			$parts = preg_split('/\s+/', $name) ?: [];

			// Expected: [title, firstName, lastName]
			if (count($parts) === 3) {
				return [
						new Person(
								title: $parts[0],
								firstName: $parts[1],
								initial: null,
								lastName: $parts[2],
						),
				];
			}

			return [];
		}
	}