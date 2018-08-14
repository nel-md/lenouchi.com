<?php
namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
	/**
	 * @ORM\OneToMany(targetEntity="Dictionary", mappedBy="author")
	 */
	private $words;

    public function __construct()
    {
        parent::__construct();
		$this->words = new ArrayCollection();
        // your own logic
    }
	
	public function __toString()
    {
        return $this->username;
    }

    /**
     * Add word
     *
     * @param \AppBundle\Entity\Dictionary $word
     *
     * @return User
     */
    public function addWord(\AppBundle\Entity\Dictionary $word)
    {
        $this->words[] = $word;

        return $this;
    }

    /**
     * Remove word
     *
     * @param \AppBundle\Entity\Dictionary $word
     */
    public function removeWord(\AppBundle\Entity\Dictionary $word)
    {
        $this->words->removeElement($word);
    }

    /**
     * Get words
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWords()
    {
        return $this->words;
    }
}
