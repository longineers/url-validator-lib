<?php

namespace spec\App;

use App\UrlValidator;
use PhpSpec\ObjectBehavior;

/**
 * @template TObject of object
 * @template TSubject
 * @extends ObjectBehavior<TObject, TSubject>
 */
class UrlValidatorSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(UrlValidator::class);
    }

    public function it_should_validate_a_valid_url(): void
    {
        $this->isValid('https://example.com')->shouldBe(true);
    }

    public function it_should_validate_a_url_with_unicode_characters(): void
    {
        $this->isValid('https://example®.com')->shouldBe(true);
    }

    public function it_should_validate_a_url_with_special_characters(): void
    {
        $this->isValid('https://example.com/path?query=invalid#fragment!')->shouldBe(true);
    }

    public function it_should_validate_idn_domains(): void
    {
        $this->isValid('https://xn--bcher-kva.com')->shouldBe(true);
        $this->isValid('https://bücher.com')->shouldBe(true);
    }

    public function it_should_not_validate_an_invalid_url(): void
    {
        $this->isValid('htp://invalid-url')->shouldBe(false);
    }

    public function it_should_not_validate_an_invalid_url_with_spaces(): void
    {
        $this->isValid('https://example .com')->shouldBe(false);
    }

    public function it_should_not_validate_an_invalid_url_with_unicode_characters(): void
    {
        $this->isValid('https://example.com/invalid®path')->shouldBe(false);
    }

    public function it_should_not_validate_an_invalid_url_with_spaces_characters(): void
    {
        $this->isValid('https://example .com/path')->shouldBe(false);
    }

    public function it_should_not_validate_an_invalid_url_with_empty_string(): void
    {
        $this->isValid('')->shouldBe(false);
    }

    public function it_should_not_validate_an_invalid_url_with_only_spaces(): void
    {
        $this->isValid('   ')->shouldBe(false);
    }

    public function it_should_not_validate_localhost_url(): void
    {
        $this->isValid('http://localhost')->shouldBe(false);
        $this->isValid('http://localhost:8080')->shouldBe(false);
        $this->isValid('http://127.0.0.1')->shouldBe(false);
        $this->isValid('http://192.168.1.1')->shouldBe(false);
        $this->isValid('http://10.0.0.1')->shouldBe(false);
        $this->isValid('http://172.16.0.1')->shouldBe(false);
    }

    public function it_should_not_validate_dangerous_protocols(): void
    {
        $this->isValid('javascript:alert(1)')->shouldBe(false);
        $this->isValid('data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==')->shouldBe(false);
        $this->isValid('file:///etc/passwd')->shouldBe(false);
        $this->isValid('vbscript:msgbox("XSS")')->shouldBe(false);
        $this->isValid('gopher://example.com')->shouldBe(false);
        $this->isValid('ldap://example.com')->shouldBe(false);
    }

    public function it_should_validate_url_with_user_pass_port_query_fragment(): void
    {
        $this->isValid('https://user:pass@example.com:8080/path?query=1#fragment')->shouldBe(true);
    }

    public function it_should_not_validate_url_missing_scheme(): void
    {
        $this->isValid('example.com')->shouldBe(false);
    }

    public function it_should_not_validate_url_with_only_host(): void
    {
        $this->isValid('example')->shouldBe(false);
    }

    public function it_should_not_validate_url_with_invalid_idn(): void
    {
        $this->isValid('https://xn--invalid_idn.com')->shouldBe(false);
    }
}
