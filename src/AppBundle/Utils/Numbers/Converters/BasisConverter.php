<?
namespace AppBundle\Utils\Numbers\Converters;

/**
 * Reverts string to new BASIS and vise versa
 *
 * Class BasisChanger
 * @package AppBundle\Utils\Numbers\Converters
 */
class BasisConverter
{
    /**
     * Do not change BASIS if system already have records which were created using current BASIS
     */
    const BASIS        = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const NUMBER_BASIS = 10;

    private $basisPower;

    public function __construct()
    {
        $this->basisPower = strlen(static::BASIS);
    }

    /**
     * Checks $string consist of BASIS chars
     */
    public function isConsistFromBasis($string)
    {
        if (!is_string($string)) {
            throw new \InvalidArgumentException('$string must have string type');
        }
        for ($i = 0; $i < strlen($string); $i++) {
            if ($this->getCharPosInBasis($string[$i]) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns $char position in BASIS or False if not in
     */
    protected function getCharPosInBasis($char)
    {
        if (!is_string($char) && strlen($char) != 1) {
            throw new \InvalidArgumentException('$char must have string type and consist from 1 symbol');
        }

        return strpos(static::BASIS, $char);
    }

    /**
     * Converts $numeric to BASIS chars
     *
     * @param $numeric
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function convert($numeric)
    {
        if (is_int($numeric)) {
            $numeric = strval($numeric);
        }
        if (!is_numeric($numeric)) {
            throw new \InvalidArgumentException('$numeric must contain numeric value');
        }
        $string = "";
        do {
            $basisCharIndex = $numeric % $this->basisPower;
            $numeric = floor($numeric / $this->basisPower);
            $string = static::BASIS[$basisCharIndex] . $string;
        } while ($numeric > 0);

        return $string;
    }

    /**
     * Reverts $string which consist of BASIS chars to numeric string
     */
    public function revert($string)
    {
        if ($this->isConsistFromBasis($string) == false) {
            throw new \InvalidArgumentException('Not BASIS-form string supplied');
        }
        $numeric = 0;
        $stringLen = strlen($string);
        $charPositionMultiplier = 1;
        for ($i = $stringLen - 1; $i >= 0; $i--, $charPositionMultiplier *= $this->basisPower) {
            $numeric += $this->getCharPosInBasis($string[$i]) * $charPositionMultiplier;
        }
        return strval($numeric);
    }
}