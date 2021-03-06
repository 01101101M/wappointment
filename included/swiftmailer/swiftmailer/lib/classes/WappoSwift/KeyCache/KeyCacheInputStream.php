<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Writes data to a KeyCache using a stream.
 *
 * @author Chris Corbyn
 */
interface WappoSwift_KeyCache_KeyCacheInputStream extends WappoSwift_InputByteStream
{
    /**
     * Set the KeyCache to wrap.
     *
     * @param WappoSwift_KeyCache $keyCache
     */
    public function setKeyCache(WappoSwift_KeyCache $keyCache);

    /**
     * Set the nsKey which will be written to.
     *
     * @param string $nsKey
     */
    public function setNsKey($nsKey);

    /**
     * Set the itemKey which will be written to.
     *
     * @param string $itemKey
     */
    public function setItemKey($itemKey);

    /**
     * Specify a stream to write through for each write().
     *
     * @param WappoSwift_InputByteStream $is
     */
    public function setWriteThroughStream(WappoSwift_InputByteStream $is);

    /**
     * Any implementation should be cloneable, allowing the clone to access a
     * separate $nsKey and $itemKey.
     */
    public function __clone();
}
