<?
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="link",
 *     indexes={
 *      @Index(name="idx_url", columns={"url"}),
 *      @Index(name="idx_date_active_to", columns={"date_active_to"})
 *     }
 * )
 */
class Link
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $url;

    /**
     * @ORM\Column(type="integer")
     */
    private $count;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_active_to;

    public function __construct()
    {
        $this->count = 0;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Link
     */
    public function setUrl($url)
    {
        $url = static::UrlFormat($url);
        $this->url = $url;

        return $this;
    }

    public static function UrlFormat($url)
    {
        $url = htmlspecialchars($url);
        if (preg_match('/^(http[s]?:)?\/\/.*/i', $url) == false) {
            $url = "//" . $url;
        }

        return $url;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set dateActiveTo
     *
     * @param string $dateActiveTo
     *
     * @return Link
     */
    public function setDateActiveTo($dateActiveTo)
    {
        $this->date_active_to = $dateActiveTo;

        return $this;
    }

    /**
     * Get dateActiveTo
     *
     * @return string
     */
    public function getDateActiveTo()
    {
        return $this->date_active_to;
    }

    /**
     * Set count
     *
     * @param integer $count
     *
     * @return Link
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Set count
     * @return Link
     */
    public function incCount($c = null)
    {
        if ($c === null || !is_numeric($c)) {
            $this->count++;
        } else {
            $this->count += $c;
        }

        return $this;
    }

    /**
     * Get count
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }
}
