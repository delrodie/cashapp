<?php

namespace App\Entity\Archive;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExtTranslations
 *
 * @ORM\Table(name="ext_translations", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={"locale", "object_class", "field", "foreign_key"})}, indexes={@ORM\Index(name="translations_lookup_idx", columns={"locale", "object_class", "foreign_key"})})
 * @ORM\Entity
 */
class ExtTranslations
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=8, nullable=false)
     */
    private $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="object_class", type="string", length=255, nullable=false)
     */
    private $objectClass;

    /**
     * @var string
     *
     * @ORM\Column(name="field", type="string", length=32, nullable=false)
     */
    private $field;

    /**
     * @var string
     *
     * @ORM\Column(name="foreign_key", type="string", length=64, nullable=false)
     */
    private $foreignKey;

    /**
     * @var string|null
     *
     * @ORM\Column(name="content", type="text", length=0, nullable=true)
     */
    private $content;


}
