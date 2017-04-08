<?
namespace AppBundle\Utils\Numbers\Converters;
use PHPUnit\Framework\TestCase;

/**
 * Reverts string to new BASIS and vise versa
 *
 * Class BasisChanger
 * @package AppBundle\Utils\Numbers\Converters
 */
class BasisConverterTest extends TestCase
{
    /**
     * Expected and input data and vise versa for Revert case
     */
    private $testData;

    /**
     * @var BasisConverter basisConverter
     */
    protected $basisConverter;

    protected function setUp()
    {
        $this->basisConverter = new BasisConverter();
        $this->testData = [
            0 => "0",
            37 => "B",
            61 => "Z",
            62 => "10",
            64 => "12",
        ];
    }

    public function testConvert()
    {
        foreach ($this->testData as $input => $expected) {
            $this->assertEquals(
                $expected,
                $this->basisConverter->convert($input)
                , "input= $input expected= $expected"
            );
        }
    }

    public function testRevert()
    {
        foreach ($this->testData as $expected => $input) {
            $this->assertEquals(
                $expected,
                $this->basisConverter->revert($input)
                , "input= $input expected= $expected"
            );
        }
    }

    public function testConvertRevert()
    {
        for ($i = 0; $i < 1000; $i++) {
            $this->assertEquals(
                $i,
                $this->basisConverter->revert(
                    $this->basisConverter->convert($i)
                )
            );
        }
    }

    public function testConvertNotConvertableValue()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->basisConverter->convert("1a");
    }

    public function testRevertNotFromBasisValue()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->basisConverter->revert("1`");
    }

    public function testFunctionIsConsistFromBasis()
    {
        $this->assertEquals(
            false,
            $this->basisConverter->isConsistFromBasis("1`")
        );
    }
}