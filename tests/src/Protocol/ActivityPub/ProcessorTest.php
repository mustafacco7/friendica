<?php

// Copyright (C) 2010-2024, the Friendica project
// SPDX-FileCopyrightText: 2010-2024 the Friendica project
//
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace Friendica\Test\src\Protocol\ActivityPub;

use PHPUnit\Framework\TestCase;

class ProcessorTest extends TestCase
{
	public function dataNormalizeMentionLinks(): array
	{
		return [
			'one-link-@' => [
				'expected' => '@[url=https://example.com]Example[/url]',
				'body'     => '[url=https://example.com]@Example[/url]',
			],
			'one-link-#' => [
				'expected' => '#[url=https://example.com]Example[/url]',
				'body'     => '[url=https://example.com]#Example[/url]',
			],
			'one-link-!' => [
				'expected' => '![url=https://example.com]Example[/url]',
				'body'     => '[url=https://example.com]!Example[/url]',
			],
			'wrong-hash-char' => [
				'expected' => '[url=https://example.com]%Example[/url]',
				'body'     => '[url=https://example.com]%Example[/url]',
			],
			'multiple-links' => [
				'expected' => '@[url=https://example.com]Example[/url] #[url=https://example.com]Example[/url] ![url=https://example.com]Example[/url]',
				'body'     => '[url=https://example.com]@Example[/url] [url=https://example.com]#Example[/url] [url=https://example.com]!Example[/url]',
			],
			'already-correct-format' => [
				'expected' => '@[url=https://example.com]Example[/url] #[url=https://example.com]Example[/url] ![url=https://example.com]Example[/url]',
				'body'     => '@[url=https://example.com]Example[/url] #[url=https://example.com]Example[/url] ![url=https://example.com]Example[/url]',
			],
			'mixed-format' => [
				'expected' => '@[url=https://example.com]Example[/url] #[url=https://example.com]Example[/url] ![url=https://example.com]Example[/url] @[url=https://example.com]Example[/url] #[url=https://example.com]Example[/url] ![url=https://example.com]Example[/url]',
				'body'     => '[url=https://example.com]@Example[/url] [url=https://example.com]#Example[/url] [url=https://example.com]!Example[/url] @[url=https://example.com]Example[/url] #[url=https://example.com]Example[/url] ![url=https://example.com]Example[/url]',
			],
		];
	}

	/**
	 * @dataProvider dataNormalizeMentionLinks
	 *
	 * @param string $expected
	 * @param string $body
	 */
	public function testNormalizeMentionLinks(string $expected, string $body)
	{
		$this->assertEquals($expected, ProcessorMock::normalizeMentionLinks($body));
	}

	public function dataAddMentionLinks(): array
	{
		return [
			'issue-10603' => [
				'expected' => '@[url=https://social.wake.st/users/liaizon]liaizon@social.wake.st[/url] @[url=https://friendica.mrpetovan.com/profile/hypolite]hypolite@friendica.mrpetovan.com[/url] yes<br /><br />',
				'body'     => '@liaizon@social.wake.st @hypolite@friendica.mrpetovan.com yes<br /><br />',
				'tags'     => [
					[
						'type' => 'Mention',
						'href' => 'https://social.wake.st/users/liaizon',
						'name' => '@liaizon@social.wake.st'
					],
					[
						'type' => 'Mention',
						'href' => 'https://friendica.mrpetovan.com/profile/hypolite',
						'name' => '@hypolite@friendica.mrpetovan.com'
					]
				],
			],
			'issue-10617' => [
				'expected' => '@[url=https://mastodon.technology/@sergey_m]sergey_m[/url]',
				'body'     => '@[url=https://mastodon.technology/@sergey_m]sergey_m[/url]',
				'tags'     => [
					[
						'type' => 'Mention',
						'href' => 'https://mastodon.technology/@sergey_m',
						'name' => '@sergey_m'
					],
				],
			],
		];
	}

	/**
	 * @dataProvider dataAddMentionLinks
	 *
	 * @param string $expected
	 * @param string $body
	 * @param array $tags
	 */
	public function testAddMentionLinks(string $expected, string $body, array $tags)
	{
		$this->assertEquals($expected, ProcessorMock::addMentionLinks($body, $tags));
	}
}
