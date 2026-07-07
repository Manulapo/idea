<?php

declare(strict_types=1);

namespace App;

enum TeamRole: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case MEMBER = 'member';

    // Returns the label for the enum value
    public function label(): string
    {
        return match ($this) {
            self::OWNER => 'Owner',
            self::ADMIN => 'Admin',
            self::MEMBER => 'Member',
        };
    }

    public static function values(): array
    {
        $values = [];
        foreach (self::cases() as $case) {
            $values[] = $case->value;
        }

        return $values;
    }

    public function canManageUsers(): bool
    {
        return $this === self::OWNER || $this === self::ADMIN;
    }

    public static function canManageRole(self $targetRole): bool
    {
        return match ($targetRole) {
            self::OWNER => true,
            self::ADMIN => $targetRole !== self::OWNER,
            self::MEMBER => false,
        };
    }
}
