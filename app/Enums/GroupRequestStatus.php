<?php

namespace App\Enums;

enum GroupRequestStatus: string
{
    case PENDING = 'pending';
    case UNDER_REVIEW = 'under_review';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case NEEDS_REVISION = 'needs_revision';

    /**
     * Get display name for the status
     */
    public function getDisplayName(): string
    {
        return match($this) {
            self::PENDING => 'Chờ duyệt',
            self::UNDER_REVIEW => 'Đang xem xét',
            self::APPROVED => 'Đã duyệt',
            self::REJECTED => 'Từ chối',
            self::NEEDS_REVISION => 'Cần chỉnh sửa',
        };
    }

    /**
     * Get color for UI display
     */
    public function getColor(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::UNDER_REVIEW => 'info',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::NEEDS_REVISION => 'secondary',
        };
    }

    /**
     * Get icon for UI display
     */
    public function getIcon(): string
    {
        return match($this) {
            self::PENDING => 'fas fa-clock',
            self::UNDER_REVIEW => 'fas fa-eye',
            self::APPROVED => 'fas fa-check-circle',
            self::REJECTED => 'fas fa-times-circle',
            self::NEEDS_REVISION => 'fas fa-edit',
        };
    }

    /**
     * Check if status allows editing
     */
    public function canBeEdited(): bool
    {
        return in_array($this, [self::PENDING, self::NEEDS_REVISION]);
    }

    /**
     * Check if status is final
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::APPROVED, self::REJECTED]);
    }

    /**
     * Check if status is actionable by admin
     */
    public function isActionableByAdmin(): bool
    {
        return in_array($this, [self::PENDING, self::UNDER_REVIEW, self::NEEDS_REVISION]);
    }

    /**
     * Get valid transition states
     */
    public function getValidTransitions(): array
    {
        return match($this) {
            self::PENDING => [self::UNDER_REVIEW, self::APPROVED, self::REJECTED],
            self::UNDER_REVIEW => [self::APPROVED, self::REJECTED, self::NEEDS_REVISION],
            self::NEEDS_REVISION => [self::PENDING, self::UNDER_REVIEW],
            self::APPROVED, self::REJECTED => [], // Final states
        };
    }

    /**
     * Check if can transition to another status
     */
    public function canTransitionTo(GroupRequestStatus $newStatus): bool
    {
        return in_array($newStatus, $this->getValidTransitions());
    }

    /**
     * Get all statuses as array
     */
    public static function toArray(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    /**
     * Get statuses with display names
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getDisplayName();
        }
        return $options;
    }

    /**
     * Get actionable statuses for admin
     */
    public static function getActionableStatuses(): array
    {
        return [
            self::PENDING,
            self::UNDER_REVIEW,
            self::NEEDS_REVISION,
        ];
    }
}
