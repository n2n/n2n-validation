<?php
/*
 * Copyright (c) 2012-2016, Hofmänner New Media.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 *
 * This file is part of the N2N FRAMEWORK.
 *
 * The N2N FRAMEWORK is free software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * N2N is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details: http://www.gnu.org/licenses/
 *
 * The following people participated in this project:
 *
 * Andreas von Burg.....: Architect, Lead Developer
 * Bert Hofmänner.......: Idea, Frontend UI, Community Leader, Marketing
 * Thomas Günther.......: Developer, Hangar
 */
namespace n2n\validation\plan;

use n2n\util\type\ArgUtils;
use n2n\l10n\Message;
use n2n\util\magic\MagicArray;
use n2n\util\magic\MagicContext;
use n2n\l10n\N2nLocale;
use n2n\util\magic\MagicObjectUnavailableException;
use PhpParser\Error;

class ErrorMap implements MagicArray, \JsonSerializable {
	private $messages = [];
	private $children = [];
	
	function __construct(array $messages = []) {
		$this->setMessages($messages);
	}
	
	/**
	 * @return Message[]
	 */
	function getAllMessages(): array {
		$messages = $this->messages;
		foreach ($this->children as $child) {
			array_push($messages, ...$child->getAllMessages());
		}
		return $messages;
	}

	/**
	 * @param N2nLocale $n2nLocale
	 * @param string|null $moduleNamespace
	 * @return array<string>
	 */
	function tAllMessages(N2nLocale $n2nLocale, string $moduleNamespace = null): array {
		return array_map(fn (Message $m) => $m->t($n2nLocale, $moduleNamespace), $this->getAllMessages());
	}
	
	/**
	 * @return Message[]
	 */
	function getMessages(): array {
		return $this->messages;
	}
	
	/**
	 * @param Message[] $messages
	 */
	function setMessages(array $messages): static {
		ArgUtils::valArray($messages, Message::class);
		$this->messages = $messages;
		return $this;
	}
	
	/**
	 * @param Message $message
	 * @return ErrorMap
	 */
	function addMessage(Message $message): static {
		$this->messages[] = $message;
		return $this;
	}
	
	/**
	 * @param array $keys
	 * @param Message $message
	 * @return ErrorMap
	 */
	function addDecendantMessage(array $keys, Message $message): static {
		if (empty($keys)) {
			$this->addMessage($message);
			return $this;
		}
		
		$this->getOrCreateChild(array_shift($keys))->addDecendantMessage($keys, $message);
		
		return $this;
	}
	
	/**
	 * @return ErrorMap[]
	 */
	function getChildren(): array {
		return $this->children;
	}
	
	/**
	 * @param ErrorMap[] $children
	 * @return ErrorMap
	 */
	function setChildren(array $children): static {
		ArgUtils::valArray($children, ErrorMap::class);
		$this->children = $children;
		return $this;
	}
	
	/**
	 * @param ErrorMap $errorMap
	 */
	function putChild(string $key, ErrorMap $errorMap): static {
		$this->children[$key] = $errorMap;
		
		return $this;
	}
	
	/**
	 * @param string $key
	 * @return ErrorMap|null
	 */
	function getChild(string $key): ?ErrorMap {
		return $this->children[$key] ?? null;
	}
	
	/**
	 * @param string $key
	 * @return ErrorMap|null
	 */
	function getOrCreateChild(string $key): ?ErrorMap {
		if (!isset($this->children[$key])) {
			$this->children[$key] = new ErrorMap(); 
		}
		
		return $this->children[$key];
	}

	/**
	 * @param array $keys
	 * @param ErrorMap $errorMap
	 * @return static
	 * @deprecated use {@link self::putDescendant()}
	 */
	function putDecendant(array $keys, ErrorMap $errorMap): static {
		$this->putDescendant($keys, $errorMap);
		return $this;
	}

	/**
	 * @param array $keys
	 * @param ErrorMap $errorMap
	 * @return ErrorMap
	 */
	function putDescendant(array $keys, ErrorMap $errorMap): static {
		if (empty($keys)) {
			throw new \InvalidArgumentException('No keys passed.');
		}
		
		$key = array_shift($keys);
		if (empty($keys)) {
			$this->putChild($key, $errorMap);
			return $this;
		}
		
		$this->getOrCreateChild($key)->putDecendant($keys, $errorMap);
		
		return $this;
	}

	function getDescendant(array $keys): ?ErrorMap {
		if (empty($keys)) {
			return null;
		}

		$key = array_shift($keys);
		return $this->getChild($key)?->getDescendant($keys);
	}

	/**
	 * @return bool
	 */
	function isEmpty() {
		if (!empty($this->messages)) {
			return false;
		}
		
		foreach ($this->children as $child) {
			if (!$child->isEmpty()) {
				return false;
			}
		}
		
		return true;
	}

	/**
	 * @param MagicContext $magicContext
	 * @return array
	 * @throws MagicObjectUnavailableException
	 */
	function toArray(MagicContext $magicContext): array {
		$n2nLocale = $magicContext->lookup(N2nLocale::class, false);

		$messageStrs = [];
		foreach ($this->messages as $key => $message) {
			if ($n2nLocale === null) {
				$messageStrs[$key] = (string) $message;
			} else {
				$messageStrs[$key] = $message->t($n2nLocale);
			}
		}
		
		return [
			'messages' => $messageStrs,
			'properties' => array_map(function ($child) use ($magicContext) { return $child->toArray($magicContext); }, 
					$this->getNotEmptyChildren())
		];
	}

	/**
	 * @return array
	 */
	function jsonSerialize(): mixed {
		$arr = [];
		
		if (!empty($this->messages)) {
			$arr['messages'] = array_map(function ($message) { return (string) $message; }, $this->messages);
		}
		
		$children = $this->getNotEmptyChildren();
		if (!empty($children)) {
			$arr['properties'] = $children;
		}
		
		return $arr;
	}
	
	/**
	 * @return ErrorMap[];
	 */
	private function getNotEmptyChildren() {
		return array_filter($this->children, function ($child) { return !$child->isEmpty(); });
	}

	static function from(string|Message|null $message): ErrorMap {
		$message = Message::build($message);

		if ($message === null) {
			return new ErrorMap();
		}

		return new ErrorMap([$message]);
	}

}
