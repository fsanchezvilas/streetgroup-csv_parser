<?php

	namespace App\Services;
	use App\DTO\Person;

	class NameParser
	{
		/**
		 * Titles observed in the provided CSV fixture.
		 */
		private const array TITLES = ['Mr', 'Mrs', 'Mister', 'Ms', 'Dr', 'Prof'];

		/**
		 * @return array<int, Person>
		 */

		public function parse(string $name): array
		{
			$name = trim($name);

			if ($name === '') {
				return [];
			}

			$parts = preg_split('/\s+/', $name) ?: [];

			if (count($parts) < 1 || ! $this->isKnownTitle($parts[0])) {
				return [];
			}

			// Expected: [title, firstName, lastName]
			if (count($parts) === 3) {
				return [
						new Person(
							title: $parts[0],
							firstName: $parts[1],
							lastName: $parts[2],
							initial: null,
						),
				];
			}

			return [];
		}
		private function isKnownTitle(string $token): bool
		{
			return in_array($token, self::TITLES, true);
		}
	}