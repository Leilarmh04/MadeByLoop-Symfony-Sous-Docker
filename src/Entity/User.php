<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cet email.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(type: 'string', length: 20)]
    private string $sellerRole = 'buyer';

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePicture = null;

    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'seller', orphanRemoval: true)]
    private Collection $products;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Cart $cart = null;

    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'buyer')]
    private Collection $orders;

    #[ORM\OneToMany(targetEntity: Conversation::class, mappedBy: 'buyer')]
    private Collection $conversations;

    #[ORM\OneToMany(targetEntity: Conversation::class, mappedBy: 'seller')]
    private Collection $sellerConversations;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'sender')]
    private Collection $messages;

    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'user')]
    private Collection $reviews;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\ManyToMany(targetEntity: Product::class)]
    #[ORM\JoinTable(name: 'user_favorites')]
    private Collection $favorites;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'followers')]
    #[ORM\JoinTable(name: 'user_following')]
    private Collection $following;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'following')]
    private Collection $followers;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->conversations = new ArrayCollection();
        $this->sellerConversations = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->followers = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }
    public function getUserIdentifier(): string { return $this->email; }

    public function getRoles(): array
    {
        if (empty($this->roles)) { return ['ROLE_USER']; }
        return $this->roles;
    }

    public function setRoles(array $roles): static { $this->roles = $roles; return $this; }
    public function getSellerRole(): string { return $this->sellerRole; }
    public function setSellerRole(string $sellerRole): static { $this->sellerRole = $sellerRole; return $this; }
    public function getBio(): ?string { return $this->bio; }
    public function setBio(?string $bio): static { $this->bio = $bio; return $this; }
    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }
    public function eraseCredentials(): void {}

    public function getProfilePicture(): ?string { return $this->profilePicture; }
    public function setProfilePicture(?string $profilePicture): static { $this->profilePicture = $profilePicture; return $this; }

    public function getProducts(): Collection { return $this->products; }
    public function addProduct(Product $product): static { if (!$this->products->contains($product)) { $this->products->add($product); $product->setSeller($this); } return $this; }
    public function removeProduct(Product $product): static { if ($this->products->removeElement($product)) { if ($product->getSeller() === $this) { $product->setSeller(null); } } return $this; }

    public function getCart(): ?Cart { return $this->cart; }
    public function setCart(Cart $cart): static { if ($cart->getUser() !== $this) { $cart->setUser($this); } $this->cart = $cart; return $this; }

    public function getOrders(): Collection { return $this->orders; }
    public function addOrder(Order $order): static { if (!$this->orders->contains($order)) { $this->orders->add($order); $order->setBuyer($this); } return $this; }
    public function removeOrder(Order $order): static { if ($this->orders->removeElement($order)) { if ($order->getBuyer() === $this) { $order->setBuyer(null); } } return $this; }

    public function getConversations(): Collection { return $this->conversations; }
    public function addConversation(Conversation $conversation): static { if (!$this->conversations->contains($conversation)) { $this->conversations->add($conversation); $conversation->setBuyer($this); } return $this; }
    public function removeConversation(Conversation $conversation): static { if ($this->conversations->removeElement($conversation)) { if ($conversation->getBuyer() === $this) { $conversation->setBuyer(null); } } return $this; }

    public function getSellerConversations(): Collection { return $this->sellerConversations; }
    public function addSellerConversation(Conversation $c): static { if (!$this->sellerConversations->contains($c)) { $this->sellerConversations->add($c); $c->setSeller($this); } return $this; }
    public function removeSellerConversation(Conversation $c): static { if ($this->sellerConversations->removeElement($c)) { if ($c->getSeller() === $this) { $c->setSeller(null); } } return $this; }

    public function getMessages(): Collection { return $this->messages; }
    public function addMessage(Message $m): static { if (!$this->messages->contains($m)) { $this->messages->add($m); $m->setSender($this); } return $this; }
    public function removeMessage(Message $m): static { if ($this->messages->removeElement($m)) { if ($m->getSender() === $this) { $m->setSender(null); } } return $this; }

    public function getReviews(): Collection { return $this->reviews; }
    public function addReview(Review $r): static { if (!$this->reviews->contains($r)) { $this->reviews->add($r); $r->setUser($this); } return $this; }
    public function removeReview(Review $r): static { if ($this->reviews->removeElement($r)) { if ($r->getUser() === $this) { $r->setUser(null); } } return $this; }

    public function getUsername(): ?string { return $this->username; }
    public function setUsername(?string $username): static { $this->username = $username; return $this; }

    public function getFavorites(): Collection { return $this->favorites; }
    public function addFavorite(Product $product): static { if (!$this->favorites->contains($product)) { $this->favorites->add($product); } return $this; }
    public function removeFavorite(Product $product): static { $this->favorites->removeElement($product); return $this; }
    public function isFavorite(Product $product): bool { return $this->favorites->contains($product); }

    public function getFollowing(): Collection { return $this->following; }
    public function getFollowers(): Collection { return $this->followers; }
    public function follow(User $user): static { if (!$this->following->contains($user) && $user !== $this) { $this->following->add($user); } return $this; }
    public function unfollow(User $user): static { $this->following->removeElement($user); return $this; }
    public function isFollowing(User $user): bool { return $this->following->contains($user); }
}