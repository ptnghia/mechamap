<?php

namespace App\Enums;

enum GroupRole: string
{
    case CREATOR = 'creator';
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';
    case MEMBER = 'member';

    /**
     * Get display name for the role
     */
    public function getDisplayName(): string
    {
        return match($this) {
            self::CREATOR => 'Người tạo',
            self::ADMIN => 'Quản trị viên',
            self::MODERATOR => 'Điều hành viên',
            self::MEMBER => 'Thành viên',
        };
    }

    /**
     * Get power level for role hierarchy
     */
    public function getPowerLevel(): int
    {
        return match($this) {
            self::CREATOR => 100,
            self::ADMIN => 75,
            self::MODERATOR => 50,
            self::MEMBER => 25,
        };
    }

    /**
     * Get color for UI display
     */
    public function getColor(): string
    {
        return match($this) {
            self::CREATOR => 'danger',
            self::ADMIN => 'warning',
            self::MODERATOR => 'info',
            self::MEMBER => 'secondary',
        };
    }

    /**
     * Get icon for UI display
     */
    public function getIcon(): string
    {
        return match($this) {
            self::CREATOR => 'fas fa-crown',
            self::ADMIN => 'fas fa-user-shield',
            self::MODERATOR => 'fas fa-user-cog',
            self::MEMBER => 'fas fa-user',
        };
    }

    /**
     * Check if role has management privileges
     */
    public function hasManagementPrivileges(): bool
    {
        return in_array($this, [self::CREATOR, self::ADMIN]);
    }

    /**
     * Check if role can moderate content
     */
    public function canModerateContent(): bool
    {
        return in_array($this, [self::CREATOR, self::ADMIN, self::MODERATOR]);
    }

    /**
     * Get all roles as array
     */
    public static function toArray(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    /**
     * Get roles with display names
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getDisplayName();
        }
        return $options;
    }
}
