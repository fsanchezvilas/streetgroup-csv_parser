<?php

	namespace App\Services;

	use App\DTO\Person;
	use Illuminate\Support\Str;

	class NameParser
	{
		private const TITLES = ['Mr', 'Mrs', 'Mister', 'Ms', 'Dr', 'Prof'];
		private const JOINERS = ['and', '&'];

		/**
		 * @return array<int, Person>
		 */
		public function parse(string $name): array
		{
			$name = trim($name);

			if ($name === '') {
				return [];
			}

			$joiner = $this->detectJoiner($name);

			if ($joiner !== null) {
				$people = $this->parseWithJoiner($name, $joiner);

				// If we cannot handle this joiner format yet, fall back to single parsing.
				if (count($people) > 0) {
					return $people;
				}
			}

			return $this->parseSingle($name);
		}
		/**
		 * @return array<int, Person>
		 */
		private function parseWithJoiner(string $name, string $joiner): array
		{
			$tokens = preg_split('/\s+/', trim($name)) ?: [];

			if ($joiner === 'and' && $this->isMrAndMrsSharedLastName($tokens)) {
				return $this->makeMrAndMrsSharedLastName($tokens[3]);
			}

			$pair = $this->splitByJoiner($name, $joiner);
			if ($pair === null) {
				return [];
			}

			return [
					...$this->parseSingle($pair[0]),
					...$this->parseSingle($pair[1]),
			];
		}

		/**
		 * Tokens: ["Mr", "and", "Mrs", "Smith"]
		 *
		 * @param array<int, string> $tokens
		 */
		private function isMrAndMrsSharedLastName(array $tokens): bool
		{
			return count($tokens) === 4
					&& $tokens[0] === 'Mr'
					&& $tokens[1] === 'and'
					&& $tokens[2] === 'Mrs'
					&& $tokens[3] !== '';
		}

		/**
		 * @return array<int, Person>
		 */
		private function makeMrAndMrsSharedLastName(string $lastName): array
		{
			return [
					new Person(title: 'Mr', firstName: null, lastName: $lastName, initial: null),
					new Person(title: 'Mrs', firstName: null, lastName: $lastName, initial: null),
			];
		}

		private function detectJoiner(string $name): ?string
		{
			foreach (self::JOINERS as $joiner) {
				if (Str::contains($name, ' ' . $joiner . ' ')) {
					return $joiner;
				}
			}

			return null;
		}

		/**
		 * @return array{0:string,1:string}|null
		 */
		private function splitByJoiner(string $name, string $joiner): ?array
		{
			$needle = ' ' . $joiner . ' ';
			$pieces = explode($needle, $name, 2);

			if (count($pieces) !== 2) {
				return null;
			}

			$left = trim($pieces[0]);
			$right = trim($pieces[1]);

			if ($left === '' || $right === '') {
				return null;
			}

			return [$left, $right];
		}

		/**
		 * @return array<int, Person>
		 */
		private function parseSingle(string $name): array
		{
			$tokens = preg_split('/\s+/', trim($name)) ?: [];

			if (count($tokens) !== 3) {
				return [];
			}

			if (! in_array($tokens[0], self::TITLES, true)) {
				return [];
			}

			$title = $tokens[0];
			$middle = $tokens[1];
			$lastName = $tokens[2];

			$firstName = $middle;
			$initial = null;

			// Handle initials like "M" or "F." from the provided CSV fixture.
			if ($this->isInitialToken($middle)) {
				$firstName = null;
				$initial = $this->normalizeInitial($middle);
			}

			return [
					new Person(
							title: $title,
							firstName: $firstName,
							lastName: $lastName,
							initial: $initial,
					),
			];
		}


		/**
		 * Returns true for tokens like "M" or "F." (no regex, fixture-scoped).
		 */
		private function isInitialToken(string $token): bool
		{
			$token = trim($token);

			if ($token === '') {
				return false;
			}

			$token = rtrim($token, '.');

			return strlen($token) === 1 && ctype_alpha($token);
		}

		/**
		 * Normalize "f" / "F." into "F".
		 */
		private function normalizeInitial(string $token): string
		{
			return strtoupper(rtrim(trim($token), '.'));
		}
	}