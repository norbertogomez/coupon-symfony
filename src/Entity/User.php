<?php
  namespace App\Entity;

  use Doctrine\Common\Collections\ArrayCollection;
  use Doctrine\Common\Collections\Collection;
  use Doctrine\ORM\Mapping as ORM;
  use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
  use Symfony\Component\Security\Core\User\UserInterface;
  use Symfony\Component\Validator\Constraints as Assert;

  /**
   * @ORM\Entity
   * @UniqueEntity(fields="email", message="Email already taken")
   * @UniqueEntity(fields="username", message="Username already taken")
   * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
   */
  class User implements UserInterface, \Serializable
  {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    protected $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=254, unique=true)
     */
    protected $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Coupon", mappedBy="user")
     */
    private $coupons;

    public function __construct()
    {
      $this->isActive = true;
      $this->coupons = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * @param mixed $id
     * @return User
     */
    public function setId($id)
    {
      $this->id = $id;
      return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
      return $this->email;
    }

    /**
     * @param mixed $email
     * @return User
     */
    public function setEmail($email)
    {
      $this->email = $email;
      return $this;
    }
    /**
     * @return mixed
     */
    public function getisActive()
    {
      return $this->isActive;
    }
    /**
     * @param mixed $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
      $this->isActive = $isActive;
      return $this;
    }

    public function getUsername()
    {
      return $this->username;
    }

    public function getSalt()
    {
      return null;
    }

    public function getPassword()
    {
      return $this->password;
    }

    public function getPlainPassword()
    {
      return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
      $this->plainPassword = $password;
    }

    public function getRoles()
    {
      return ['ROLE_USER'];
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
      return serialize([
        $this->id,
        $this->username,
        $this->password,
      ]);
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
      list(
        $this->id,
        $this->username,
        $this->password,
      ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function eraseCredentials()
    {
      // TODO: Implement eraseCredentials() method.
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection|Coupon[]
     */
    public function getCoupons(): Collection
    {
        return $this->coupons;
    }

    public function addCoupon(Coupon $coupon): self
    {
        if (!$this->coupons->contains($coupon)) {
            $this->coupons[] = $coupon;
            $coupon->setUser($this);
        }

        return $this;
    }

    public function removeCoupon(Coupon $coupon): self
    {
        if ($this->coupons->contains($coupon)) {
            $this->coupons->removeElement($coupon);
            // set the owning side to null (unless already changed)
            if ($coupon->getUser() === $this) {
                $coupon->setUser(null);
            }
        }

        return $this;
    }
  }