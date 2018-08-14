<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoteDown
 *
 * @ORM\Table(name="vote_down")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VoteDownRepository")
 */
class VoteDown
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
     * @ORM\Column(name="user_id", type="string", length=255)
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="Dictionary", inversedBy="voteDowns")
     * @ORM\JoinColumn(name="word_id", referencedColumnName="id")
     */
    private $word;


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
     * Set userId
     *
     * @param string $userId
     *
     * @return VoteDown
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set word
     *
     * @param \AppBundle\Entity\Dictionary $word
     *
     * @return VoteDown
     */
    public function setWord(\AppBundle\Entity\Dictionary $word = null)
    {
        $this->word = $word;

        return $this;
    }

    /**
     * Get word
     *
     * @return \AppBundle\Entity\Dictionary
     */
    public function getWord()
    {
        return $this->word;
    }
}
