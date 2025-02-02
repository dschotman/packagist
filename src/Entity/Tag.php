<?php

/*
 * This file is part of Packagist.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *     Nils Adermann <naderman@naderman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="tag",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="tag_name_idx", columns={"name"})}
 * )
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(length=191)
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Version", mappedBy="tags")
     * @var Collection<int, Version>&Selectable<int, Version>
     */
    private Collection $versions;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param string                      $name
     * @param bool                        $create
     *
     * @return Tag
     * @throws \Doctrine\ORM\NoResultException
     */
    public static function getByName(EntityManager $em, $name, $create = false): Tag
    {
        try {
            $qb = $em->createQueryBuilder();
            $qb->select('t')
                ->from(__CLASS__, 't')
                ->where('t.name = ?1')
                ->setMaxResults(1)
                ->setParameter(1, $name);

            return $qb->getQuery()->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            if ($create) {
                $tag = new self($name);
                $em->persist($tag);

                return $tag;
            }
            throw $e;
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addVersions(Version $versions): void
    {
        $this->versions[] = $versions;
    }

    /**
     * @return Collection<int, Version>&Selectable<int, Version>
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}
