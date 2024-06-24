<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Licensed under BSD License
 * For full copyright and license information, please see the LICENSE file.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @link http://phpmd.org/
 */

namespace PHPMD\Node;

use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PHPMD\AbstractTest;
use PHPMD\Test\Inheritance\Bar;
use PHPMD\Test\Inheritance\Baz;
use PHPMD\Test\Inheritance\Foo;

/**
 * Test case for the method node implementation.
 *
 * @covers \PHPMD\Node\MethodNode
 * @covers \PHPMD\Node\AbstractCallableNode
 */
class MethodNodeTest extends AbstractTest
{
    /**
     * testMagicCallDelegatesToWrappedPHPDependMethod
     *
     * @return void
     */
    public function testMagicCallDelegatesToWrappedPHPDependMethod()
    {
        $method = $this->getMockFromBuilder(
            $this->getMockBuilder('PDepend\\Source\\AST\\ASTMethod')
                ->setConstructorArgs(array(null))
        );
        $method->expects($this->once())
            ->method('getStartLine');

        $node = new MethodNode($method);
        $node->getStartLine();
    }

    /**
     * testMagicCallThrowsExceptionWhenNoMatchingMethodExists
     *
     * @return void
     * @expectedException \BadMethodCallException
     */
    public function testMagicCallThrowsExceptionWhenNoMatchingMethodExists()
    {
        $node = new MethodNode(new ASTMethod(null));
        $node->getFooBar();
    }

    /**
     * testGetParentTypeReturnsInterfaceForInterfaceMethod
     *
     * @return void
     */
    public function testGetParentTypeReturnsInterfaceForInterfaceMethod()
    {
        $this->assertInstanceOf(
            'PHPMD\\Node\\InterfaceNode',
            $this->getMethod()->getParentType()
        );
    }

    /**
     * testGetParentTypeReturnsClassForClassMethod
     *
     * @return void
     */
    public function testGetParentTypeReturnsClassForClassMethod()
    {
        $this->assertInstanceOf(
            'PHPMD\\Node\\ClassNode',
            $this->getMethod()->getParentType()
        );
    }

    /**
     * @return void
     */
    public function testGetParentTypeReturnsTrait()
    {
        $this->assertInstanceOf(
            'PHPMD\\Node\\TraitNode',
            $this->getMethod()->getParentType()
        );
    }

    /**
     * testHasSuppressWarningsExecutesDefaultImplementation
     *
     * @return void
     */
    public function testHasSuppressWarningsExecutesDefaultImplementation()
    {
        $rule = $this->getRuleMock();
        $rule->setName('FooBar');

        $method = $this->getMethod();
        $this->assertTrue($method->hasSuppressWarningsAnnotationFor($rule));
    }

    /**
     * testHasSuppressWarningsDelegatesToParentClassMethod
     *
     * @return void
     */
    public function testHasSuppressWarningsDelegatesToParentClassMethod()
    {
        $rule = $this->getRuleMock();
        $rule->setName('FooBar');

        $method = $this->getMethod();
        $this->assertTrue($method->hasSuppressWarningsAnnotationFor($rule));
    }

    /**
     * testHasSuppressWarningsDelegatesToParentInterfaceMethod
     *
     * @return void
     */
    public function testHasSuppressWarningsDelegatesToParentInterfaceMethod()
    {
        $rule = $this->getRuleMock();
        $rule->setName('FooBar');

        $method = $this->getMethod();
        $this->assertTrue($method->hasSuppressWarningsAnnotationFor($rule));
    }

    /**
     * testHasSuppressWarningsIgnoresCaseFirstLetter
     *
     * @return void
     */
    public function testHasSuppressWarningsIgnoresCaseFirstLetter()
    {
        $rule = $this->getRuleMock();
        $rule->setName('FooBar');

        $method = $this->getMethod();
        $this->assertTrue($method->hasSuppressWarningsAnnotationFor($rule));
    }

    /**
     * testIsDeclarationReturnsTrueForMethodDeclaration
     *
     * @return void
     * @since 1.2.1
     */
    public function testIsDeclarationReturnsTrueForMethodDeclaration()
    {
        $method = $this->getMethod();
        $this->assertTrue($method->isDeclaration());
    }

    /**
     * testIsDeclarationReturnsTrueForMethodDeclarationWithParent
     *
     * @return void
     * @since 1.2.1
     */
    public function testIsDeclarationReturnsTrueForMethodDeclarationWithParent()
    {
        $method = $this->getMethod();
        $this->assertTrue($method->isDeclaration());
    }

    /**
     * testIsDeclarationReturnsFalseForInheritMethodDeclaration
     *
     * @return void
     * @since 1.2.1
     */
    public function testIsDeclarationReturnsFalseForInheritMethodDeclaration()
    {
        $method = $this->getMethod();
        $this->assertFalse($method->isDeclaration());
    }

    /**
     * testIsDeclarationReturnsFalseForImplementedAbstractMethod
     *
     * @return void
     * @since 1.2.1
     */
    public function testIsDeclarationReturnsFalseForImplementedAbstractMethod()
    {
        $method = $this->getMethod();
        $this->assertFalse($method->isDeclaration());
    }

    /**
     * testIsDeclarationReturnsFalseForImplementedInterfaceMethod
     *
     * @return void
     * @since 1.2.1
     */
    public function testIsDeclarationReturnsFalseForImplementedInterfaceMethod()
    {
        $method = $this->getMethod();
        $this->assertFalse($method->isDeclaration());
    }

    /**
     * @return void
     */
    public function testIsDeclarationReturnsTrueForPrivateMethod()
    {
        $method = $this->getMethod();
        $this->assertTrue($method->isDeclaration());
    }

    /**
     * testGetFullQualifiedNameReturnsExpectedValue
     *
     * @return void
     */
    public function testGetFullQualifiedNameReturnsExpectedValue()
    {
        $class = new ASTClass('MyClass');
        $class->setNamespace(new ASTNamespace('Sindelfingen'));

        $method = new ASTMethod('beer');
        $method->setParent($class);

        $node = new MethodNode($method);

        $this->assertSame('Sindelfingen\\MyClass::beer()', $node->getFullQualifiedName());
    }

    /**
     * @return void
     */
    public function testIsDeclarationReturnsFalseForInheritedDeclaration()
    {
        // Requiring these files as the autoloading is not configured for them.
        require_once __DIR__.'/../../../resources/files/classes/inheritance/Foo.php';
        require_once __DIR__.'/../../../resources/files/classes/inheritance/Bar.php';
        require_once __DIR__.'/../../../resources/files/classes/inheritance/Baz.php';

        $class = $this->getClassNodeForTestFile(__DIR__.'/../../../resources/files/classes/inheritance/Baz.php');
        $className = $class->getFullQualifiedName();

        $this->assertSame(Baz::class, $className);
        $this->assertTrue(is_subclass_of($className, Foo::class));

        $methods = $class->getMethods();

        $this->assertCount(1, $methods);
        $this->assertArrayHasKey(0, $methods);

        $parentClass = $class->getParentClass();
        $parentClassName = $parentClass->getNamespacedName();

        $this->assertSame(Bar::class, $parentClassName);
        // $this->assertTrue($parentClass->isAbstract());
        $this->assertTrue(is_subclass_of($parentClassName, Foo::class));

        $parentMethods = $parentClass->getMethods();
        $this->assertEmpty($parentMethods);

        // $interfaces = $parentClass->getInterfaces();
        // $this->assertCount(1, $interfaces);
        // $this->assertArrayHasKey(0, $interfaces);

        // $interface = $interfaces[0];

        // $this->assertSame(Foo::class, $interface->getNamespacedName());

        // $interfaceMethods = $interface->getMethods();

        // $this->assertCount(1, $interfaceMethods);
        // $this->assertArrayHasKey(0, $interfaceMethods);

        // $iterfaceMethod = $interfaceMethods[0];

        // $this->assertTrue($iterfaceMethod->isDeclaration());

        $method = $methods[0];

        $this->assertFalse($method->isDeclaration());
    }
}
