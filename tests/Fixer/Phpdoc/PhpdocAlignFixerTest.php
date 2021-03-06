<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer
 */
final class PhpdocAlignFixerTest extends AbstractFixerTestCase
{
    public function testFix()
    {
        $this->fixer->configure(['tags' => ['param']]);

        $expected = <<<'EOF'
<?php
    /**
     * @param EngineInterface $templating
     * @param string          $format
     * @param int             $code       An HTTP response status code
     * @param bool            $debug
     * @param mixed           &$reference A parameter passed by reference
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param  EngineInterface $templating
     * @param string      $format
     * @param  int  $code       An HTTP response status code
     * @param    bool         $debug
     * @param  mixed    &$reference     A parameter passed by reference
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixMultiLineDesc()
    {
        $this->fixer->configure(['tags' => ['param', 'property']]);

        $expected = <<<'EOF'
<?php
    /**
     * @param    EngineInterface $templating
     * @param    string          $format
     * @param    int             $code       An HTTP response status code
     *                                       See constants
     * @param    bool            $debug
     * @param    bool            $debug      See constants
     *                                       See constants
     * @param    mixed           &$reference A parameter passed by reference
     * @property mixed           $foo        A foo
     *                                       See constants
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param  EngineInterface $templating
     * @param string      $format
     * @param  int  $code       An HTTP response status code
     *                              See constants
     * @param    bool         $debug
     * @param    bool         $debug See constants
     * See constants
     * @param  mixed    &$reference     A parameter passed by reference
     * @property   mixed   $foo     A foo
     *                               See constants
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixMultiLineDescWithThrows()
    {
        $this->fixer->configure(['tags' => ['param', 'return', 'throws']]);

        $expected = <<<'EOF'
<?php
    /**
     * @param EngineInterface $templating
     * @param string          $format
     * @param int             $code       An HTTP response status code
     *                                    See constants
     * @param bool            $debug
     * @param bool            $debug      See constants
     *                                    See constants
     * @param mixed           &$reference A parameter passed by reference
     *
     * @return Foo description foo
     *
     * @throws Foo description foo
     *             description foo
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param  EngineInterface $templating
     * @param string      $format
     * @param  int  $code       An HTTP response status code
     *                              See constants
     * @param    bool         $debug
     * @param    bool         $debug See constants
     * See constants
     * @param  mixed    &$reference     A parameter passed by reference
     *
     * @return Foo description foo
     *
     * @throws Foo             description foo
     * description foo
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithReturnAndThrows()
    {
        $this->fixer->configure(['tags' => ['param', 'throws', 'return']]);

        $expected = <<<'EOF'
<?php
    /**
     * @param  EngineInterface $templating
     * @param  mixed           &$reference A parameter passed by reference
     * @throws Bar             description bar
     * @return Foo             description foo
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param EngineInterface       $templating
     * @param  mixed    &$reference     A parameter passed by reference
     * @throws   Bar description bar
     * @return  Foo     description foo
     */

EOF;

        $this->doTest($expected, $input);
    }

    /**
     * References the issue #55 on github issue
     * https://github.com/FriendsOfPhp/PHP-CS-Fixer/issues/55.
     */
    public function testFixThreeParamsWithReturn()
    {
        $this->fixer->configure(['tags' => ['param', 'return']]);

        $expected = <<<'EOF'
<?php
    /**
     * @param  string $param1
     * @param  bool   $param2 lorem ipsum
     * @param  string $param3 lorem ipsum
     * @return int    lorem ipsum
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param   string $param1
     * @param bool   $param2 lorem ipsum
     * @param    string $param3 lorem ipsum
     * @return int lorem ipsum
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixOnlyReturn()
    {
        $this->fixer->configure(['tags' => ['return']]);

        $expected = <<<'EOF'
<?php
    /**
     * @return Foo description foo
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @return   Foo             description foo
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testReturnWithDollarThis()
    {
        $this->fixer->configure(['tags' => ['param', 'return']]);

        $expected = <<<'EOF'
<?php
    /**
     * @param  Foo   $foo
     * @return $this
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param Foo $foo
     * @return $this
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testCustomAnnotationsStayUntouched()
    {
        $this->fixer->configure(['tags' => ['return']]);

        $expected = <<<'EOF'
<?php
    /**
     * @return string
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @return string
     *  @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testCustomAnnotationsStayUntouched2()
    {
        $this->fixer->configure(['tags' => ['var']]);

        $expected = <<<'EOF'
<?php

class X
{
    /**
     * @var Collection<Value>|Value[]
     * @ORM\ManyToMany(
     *  targetEntity="\Dl\Component\DomainModel\Product\Value\AbstractValue",
     *  inversedBy="externalAliases"
     * )
     */
    private $values;
}

EOF;

        $this->doTest($expected);
    }

    public function testFixWithVar()
    {
        $this->fixer->configure(['tags' => ['var']]);

        $expected = <<<'EOF'
<?php
    /**
     * @var Type
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var   Type
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithType()
    {
        $this->fixer->configure(['tags' => ['type']]);

        $expected = <<<'EOF'
<?php
    /**
     * @type Type
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @type   Type
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithVarAndDescription()
    {
        $this->fixer->configure(['tags' => ['var']]);

        $expected = <<<'EOF'
<?php
    /**
     * This is a variable.
     *
     * @var Type
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * This is a variable.
     *
     * @var   Type
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithVarAndInlineDescription()
    {
        $this->fixer->configure(['tags' => ['var']]);

        $expected = <<<'EOF'
<?php
    /**
     * @var Type This is a variable.
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var   Type   This is a variable.
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithTypeAndInlineDescription()
    {
        $this->fixer->configure(['tags' => ['type']]);

        $expected = <<<'EOF'
<?php
    /**
     * @type Type This is a variable.
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @type   Type   This is a variable.
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testRetainsNewLineCharacters()
    {
        $this->fixer->configure(['tags' => ['param']]);

        // when we're not modifying a docblock, then line endings shouldn't change
        $this->doTest("<?php\r    /**\r     * @param Example Hello there!\r     */\r");
    }

    public function testMalformedDocBlock()
    {
        $this->fixer->configure(['tags' => ['return']]);

        $input = <<<'EOF'
<?php
    /**
     * @return string
     * */

EOF;

        $this->doTest($input);
    }

    public function testDifferentIndentation()
    {
        $this->fixer->configure(['tags' => ['param', 'return']]);

        $expected = <<<'EOF'
<?php
/**
 * @param int    $limit
 * @param string $more
 *
 * @return array
 */

        /**
         * @param int    $limit
         * @param string $more
         *
         * @return array
         */
EOF;

        $input = <<<'EOF'
<?php
/**
 * @param   int       $limit
 * @param   string       $more
 *
 * @return array
 */

        /**
         * @param   int       $limit
         * @param   string       $more
         *
         * @return array
         */
EOF;

        $this->doTest($expected, $input);
    }

    /**
     * @param array       $config
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(array $config, $expected, $input = null)
    {
        $this->fixer->configure($config);
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        return [
            [
                ['tags' => ['type']],
                "<?php\r\n\t/**\r\n\t * @type Type This is a variable.\r\n\t */",
                "<?php\r\n\t/**\r\n\t * @type   Type   This is a variable.\r\n\t */",
            ],
            [
                ['tags' => ['param', 'return']],
                "<?php\r\n/**\r\n * @param int    \$limit\r\n * @param string \$more\r\n *\r\n * @return array\r\n */",
                "<?php\r\n/**\r\n * @param   int       \$limit\r\n * @param   string       \$more\r\n *\r\n * @return array\r\n */",
            ],
        ];
    }

    public function testCanFixBadFormatted()
    {
        $this->fixer->configure(['tags' => ['var']]);

        $expected = "<?php\n    /**\n     * @var Foo */\n";

        $this->doTest($expected);
    }

    public function testFixUnicode()
    {
        $this->fixer->configure(['tags' => ['param', 'return']]);

        $expected = <<<'EOF'
<?php
    /**
     * Method test.
     *
     * @param int      $foobar Description
     * @param string   $foo    Description
     * @param mixed    $bar    Description word_with_ą
     * @param int|null $test   Description
     */
    $a = 1;

    /**
     * @return string
     * @SuppressWarnings(PHPMD.UnusedLocalVariable) word_with_ą
     */
    $b = 1;
EOF;

        $input = <<<'EOF'
<?php
    /**
     * Method test.
     *
     * @param int    $foobar Description
     * @param string $foo    Description
     * @param mixed $bar Description word_with_ą
     * @param int|null $test Description
     */
    $a = 1;

    /**
     * @return string
     *   @SuppressWarnings(PHPMD.UnusedLocalVariable) word_with_ą
     */
    $b = 1;
EOF;

        $this->doTest($expected, $input);
    }

    public function testDoesNotAlignPropertyByDefault()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param  int       $foobar Description
     * @return int
     * @throws Exception
     * @var    FooBar
     * @type   BarFoo
     * @property     string    $foo   Hello World
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param    int   $foobar   Description
     * @return  int
     * @throws Exception
     * @var       FooBar
     * @type      BarFoo
     * @property     string    $foo   Hello World
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testAlignsProperty()
    {
        $this->fixer->configure(['tags' => ['param', 'property', 'return', 'throws', 'type', 'var']]);

        $expected = <<<'EOF'
<?php
    /**
     * @param    int       $foobar Description
     * @return   int
     * @throws   Exception
     * @var      FooBar
     * @type     BarFoo
     * @property string    $foo    Hello World
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param    int   $foobar   Description
     * @return  int
     * @throws Exception
     * @var       FooBar
     * @type      BarFoo
     * @property     string    $foo   Hello World
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDoesNotAlignWithEmptyConfig()
    {
        $this->fixer->configure(['tags' => []]);

        $input = <<<'EOF'
<?php
    /**
     * @param    int   $foobar   Description
     * @return  int
     * @throws Exception
     * @var       FooBar
     * @type      BarFoo
     * @property     string    $foo   Hello World
     */
EOF;

        $this->doTest($input);
    }

    /**
     * @param array  $config
     * @param string $expected
     * @param string $input
     *
     *
     * @dataProvider provideVariadicCases
     */
    public function testVariadicParams(array $config, $expected, $input)
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public function provideVariadicCases()
    {
        return [
            [
                ['tags' => ['param']],
                '<?php
final class Sample
{
    /**
     * @param int[] $a    A
     * @param int   &$b   B
     * @param array ...$c C
     */
    public function sample2($a, &$b, ...$c)
    {
    }
}
',
            '<?php
final class Sample
{
    /**
     * @param int[]       $a  A
     * @param int          &$b B
     * @param array ...$c    C
     */
    public function sample2($a, &$b, ...$c)
    {
    }
}
',
            ],
            [
                ['tags' => ['param']],
                '<?php
final class Sample
{
    /**
     * @param int     $a
     * @param int     $b
     * @param array[] ...$c
     */
    public function sample2($a, $b, ...$c)
    {
    }
}
',
            '<?php
final class Sample
{
    /**
     * @param int       $a
     * @param int    $b
     * @param array[]      ...$c
     */
    public function sample2($a, $b, ...$c)
    {
    }
}
',
            ],
        ];
    }
}
