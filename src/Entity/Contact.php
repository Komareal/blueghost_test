<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use JsonSerializable;

/**
 * Represents a contact entity with basic personal information.
 *
 * @property int|null $id Unique identifier for the contact
 * @property string|null $name First name of the contact
 * @property string|null $surname Last name of the contact
 * @property string|null $email Email address of the contact
 * @property string|null $phone Phone number of the contact (optional)
 * @property string|null $note Additional notes about the contact (optional)
 */
#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ORM\Table(name: "contact", uniqueConstraints: [new UniqueConstraint(name: "uniq_contact_slug", columns: ["slug"])])]
#[ORM\HasLifecycleCallbacks]
class Contact implements JsonSerializable
{

    /**
     * Email address of the contact.
     */
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    /**
     * Unique identifier for the contact.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * First name of the contact.
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * Additional notes about the contact.
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    /**
     * Phone number of the contact (optional).
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    /**
     * Slug for URL (unique).
     */
    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    /**
     * Last name of the contact.
     */
    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    /**
     * Get the email address.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the email address.
     *
     * @param string $email
     * @return static
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the unique identifier.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the first name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the first name.
     *
     * @param string $name
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the note.
     *
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * Set the note.
     *
     * @param string|null $note
     * @return static
     */
    public function setNote(?string $note): static
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get the phone number.
     *
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Set the phone number.
     *
     * @param string $phone
     * @return static
     */
    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get the slug.
     *
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set the slug.
     *
     * @param string $slug
     * @return static
     */
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get the last name.
     *
     * @return string|null
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * Set the last name.
     *
     * @param string $surname
     * @return static
     */
    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getSlug(),
            'name' => $this->getName(),
            'surname' => $this->getSurname(),
            'phone' => $this->getPhone(),
            'email' => $this->getEmail(),
            'note' => $this->getNote(),
        ];
    }

    /**
     * Generate a slug from name and surname (ASCII, not unique).
     *
     * @param string $name
     * @param string $surname
     * @return string
     */
    public static function slugify(string $name, string $surname): string
    {
        $text = $name . '-' . $surname;
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        $text = trim($text, '-');
        $text = strtolower($text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        if (empty($text)) {
            return 'n-a';
        }
        return $text;
    }
}
