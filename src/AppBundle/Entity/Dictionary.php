<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\User;

/**
 * Dictionary
 *
 * @ORM\Table(name="dictionary")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DictionaryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Dictionary
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="word", type="string", length=255)
     */
    private $word;

    /**
     * @var string
     *
     * @ORM\Column(name="definition", type="text")
     */
    private $definition;

    /**
     * @var string
     *
     * @ORM\Column(name="example", type="text")
     */
    private $example;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetimetz")
     */
    private $createdAt;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetimetz")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="VoteUp", mappedBy="word")
     */
    private $voteUps;

    /**
     * @ORM\OneToMany(targetEntity="VoteDown", mappedBy="word")
     */
    private $voteDowns;


	/**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="words")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $author;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->voteUps = new \Doctrine\Common\Collections\ArrayCollection();
        $this->voteDowns = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set word
     *
     * @param string $word
     *
     * @return Dictionary
     */
    public function setWord($word)
    {
        $this->word = $word;

        return $this;
    }

    /**
     * Get word
     *
     * @return string
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * Set definition
     *
     * @param string $definition
     *
     * @return Dictionary
     */
    public function setDefinition($definition)
    {
        $this->definition = $definition;

        return $this;
    }

    /**
     * Get definition
     *
     * @return string
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Set example
     *
     * @param string $example
     *
     * @return Dictionary
     */
    public function setExample($example)
    {
        $this->example = $example;

        return $this;
    }

    /**
     * Get example
     *
     * @return string
     */
    public function getExample()
    {
        return $this->example;
    }

     /**
 	* @ORM\PrePersist
 	*/
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

   	/**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add voteUp
     *
     * @param \AppBundle\Entity\VoteUp $voteUp
     *
     * @return Dictionary
     */
    public function addVoteUp(\AppBundle\Entity\VoteUp $voteUp)
    {
        $this->voteUps[] = $voteUp;

        return $this;
    }

    /**
     * Remove voteUp
     *
     * @param \AppBundle\Entity\VoteUp $voteUp
     */
    public function removeVoteUp(\AppBundle\Entity\VoteUp $voteUp)
    {
        $this->voteUps->removeElement($voteUp);
    }

    /**
     * Get voteUps
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoteUps()
    {
        return $this->voteUps;
    }

    /**
     * Add voteDown
     *
     * @param \AppBundle\Entity\VoteDown $voteDown
     *
     * @return Dictionary
     */
    public function addVoteDown(\AppBundle\Entity\VoteDown $voteDown)
    {
        $this->voteDowns[] = $voteDown;

        return $this;
    }

    /**
     * Remove voteDown
     *
     * @param \AppBundle\Entity\VoteDown $voteDown
     */
    public function removeVoteDown(\AppBundle\Entity\VoteDown $voteDown)
    {
        $this->voteDowns->removeElement($voteDown);
    }

    /**
     * Get voteDowns
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoteDowns()
    {
        return $this->voteDowns;
    }

    /**
     * Set author
     *
     * @param \AppBundle\Entity\User $author
     *
     * @return Dictionary
     */
    public function setAuthor(\AppBundle\Entity\User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \AppBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
