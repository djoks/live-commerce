<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CartType;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * User Model
 *
 * Represents an authenticated user in the system. Users can have customer
 * profiles, shopping carts, wishlists, and purchase invoices.
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Define attribute casting for the model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
        ];
    }

    /**
     * Get the user's initials from their name.
     *
     * Takes the first letter of up to two words in the user's name.
     * For example, "John Doe" returns "JD".
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the customer profile associated with this user.
     *
     * @return HasOne<Customer, $this>
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    /**
     * Get all shopping carts and wishlists belonging to this user.
     *
     * @return HasMany<Cart, $this>
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get all invoices (orders) placed by this user.
     *
     * @return HasMany<Invoice, $this>
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the user's current active shopping cart.
     *
     * Returns the most recently created active cart, or null if none exists.
     */
    public function activeCart(): ?Cart
    {
        return $this->carts()
            ->ofType(CartType::Cart)
            ->active()
            ->latest()
            ->first();
    }

    /**
     * Get the user's default wishlist.
     *
     * Returns the most recently created active wishlist, or null if none exists.
     */
    public function defaultWishlist(): ?Cart
    {
        return $this->carts()
            ->ofType(CartType::Wishlist)
            ->active()
            ->latest()
            ->first();
    }
}
