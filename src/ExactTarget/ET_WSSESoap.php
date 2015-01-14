<?php

namespace ExactTarget;

use Wse\WSSESoap;

/**
 * This is an ExactTarget-specific extension to the common Wse\WSSESoap
 * implementation.  These changes are released under the BSD License.
 *
 * Copyright (c) 2010, Robert Richards <rrichards@ctindustries.net>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Robert Richards nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author     Robert Richards <rrichards@ctindustries.net>
 * @author     Chris Verges <cverges@coursehero.com>
 * @copyright  2007-2010 Robert Richards <rrichards@ctindustries.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
class ET_WSSESoap extends WSSESoap
{
    /**
     * Adds an oAuth token authentication mechanism needed by the
     * ExactTarget Email SOAP API.
     *
     * @param string $token
     */
    public function addOAuth($token)
    {
		$headers = $this->SOAPXPath->query('//wssoap:Envelope/wssoap:Header');
		$header = $headers->item(0);
		if (! $header) {
			$header = $this->soapDoc->createElementNS($this->soapNS, $this->soapPFX.':Header');
			$this->envelope->insertBefore($header, $this->envelope->firstChild);
		}

		$authnode = $this->soapDoc->createElementNS('http://exacttarget.com', 'oAuth');
		$header->appendChild($authnode);

		$oauthtoken = $this->soapDoc->createElementNS(null, 'oAuthToken', $token);
		$authnode->appendChild($oauthtoken);
	}
}
