<?php
namespace Psalm\Tests;

class PropertyTypeInvarianceTest extends TestCase
{
    use Traits\InvalidCodeAnalysisTestTrait;
    use Traits\ValidCodeAnalysisTestTrait;

    /**
     * @return iterable<string,array{string,assertions?:array<string,string>,error_levels?:string[]}>
     */
    public function providerValidCodeParse(): iterable
    {
        return [
            'validcode' => [
                '<?php
                    class ParentClass
                    {
                        /** @var null|string */
                        protected $mightExist;

                        protected ?string $mightExistNative = null;

                        /** @var string */
                        protected $doesExist = "";

                        protected string $doesExistNative = "";
                    }

                    class ChildClass extends ParentClass
                    {
                        /** @var null|string */
                        protected $mightExist = "";

                        protected ?string $mightExistNative = null;

                        /** @var string */
                        protected $doesExist = "";

                        protected string $doesExistNative = "";
                    }'
            ],
            'allowTemplatedInvariance' => [
                '<?php
                    /**
                     * @template T as string|null
                     */
                    abstract class A {
                        /** @var T */
                        public $foo;
                    }

                    /**
                     * @extends A<string>
                     */
                    class AChild extends A {
                        /** @var string */
                        public $foo = "foo";
                    }'
            ],
        ];
    }

    /**
     * @return iterable<string,array{string,error_message:string,2?:string[],3?:bool,4?:string}>
     */
    public function providerInvalidCodeParse(): iterable
    {
        return [
            'variantDocblockProperties' => [
                '<?php
                    class ParentClass
                    {
                        /** @var null|string */
                        protected $mightExist;
                    }

                    class ChildClass extends ParentClass
                    {
                        /** @var string */
                        protected $mightExist = "";
                    }',
                'error_message' => 'NonInvariantDocblockPropertyType',
            ],
            'variantProperties' => [
                '<?php
                    class ParentClass
                    {
                        protected ?string $mightExist = null;
                    }

                    class ChildClass extends ParentClass
                    {
                        protected string $mightExist = "";
                    }',
                'error_message' => 'NonInvariantPropertyType',
            ],
        ];
    }
}
