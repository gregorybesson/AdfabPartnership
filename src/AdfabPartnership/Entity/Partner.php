<?php
namespace AdfabPartnership\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterInterface;

/**
 * Participation to a game. After having subscribed to a game, the player
 * can play (one or more times). An entry represent a game session.
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(name="partner")
 */
class Partner
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $active = 1;

    /**
     * @ORM\Column("bounce_page", type="boolean", nullable=true)
     */
    protected $bouncePage = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $logo;

    /**
     * @ORM\Column(name="small_logo", type="string", length=255, nullable=true)
     */
    protected $smallLogo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $website;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $facebook;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $newsletter = 0;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    public function __construct()
    {
    }

    /** @PrePersist */
    public function createChrono()
    {
        $this->createdAt = new \DateTime("now");
        $this->updatedAt = new \DateTime("now");
    }

    /** @PreUpdate */
    public function updateChrono()
    {
        $this->updatedAt = new \DateTime("now");
    }

    /**
     * @return the unknown_type
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param unknown_type $id
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return the status
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param field_type $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return
     */
    public function getBouncePage()
    {
        return $this->bouncePage;
    }

    /**
     * @param field_type $bouncePage
     */
    public function setBouncePage($bouncePage)
    {
        $this->bouncePage = $bouncePage;
    }

    /**
     * @return the unknown_type
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param unknown_type $name
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return the string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return the unknown_type
     */
    public function getSmallLogo()
    {
        return $this->smallLogo;
    }

    /**
     * @param unknown_type $name
     */
    public function setSmallLogo($smallLogo)
    {
        $this->smallLogo = $smallLogo;

        return $this;
    }

    /**
     * @return the string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @return the string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * @param string $facebook
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * @return the string
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * @param string $newsletter
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;

        return $this;
    }
    /**
     * @return the $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return the $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function populate($data = array())
    {
        if (isset($data['name']) && $data['name'] != null) {
            $this->name = $data['name'];
        }

        if (isset($data['active']) && $data['active'] != null) {
            $this->active = $data['active'];
        }

        if (isset($data['bouncePage']) && $data['bouncePage'] != null) {
            $this->bouncePage = $data['bouncePage'];
        }

        if (isset($data['logo']) && $data['logo'] != null) {
            $this->logo = $data['logo'];
        }

        if (isset($data['smallLogo']) && $data['smallLogo'] != null) {
            $this->smallLogo = $data['smallLogo'];
        }

        if (isset($data['website']) && $data['website'] != null) {
            $this->website = $data['website'];
        }

        if (isset($data['facebook']) && $data['facebook'] != null) {
            $this->facebook = $data['facebook'];
        }

        if (isset($data['newsletter']) && $data['newsletter'] != null) {
            $this->newsletter = $data['newsletter'];
        }
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name' => 'id',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'name',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 5,
                            'max' => 255
                        )
                    )
                )
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
