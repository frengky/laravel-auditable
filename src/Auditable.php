<?php

namespace Frengky\Auditable;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
	/**
     * Auditable attributes excluded from the Audit.
     *
     * @var array
     */
    protected $auditExcludedAttributes = [
    	'id'
    ];

    /**
     * The attribute is auditable when explicitly, listed or when the include array is empty
     *
     * @var array
     */
    protected $auditIncludedAttributes = [];

    public function auditEventWithValues($event, array $newValues, array $oldValues = [])
    {
        return $this->audits()->save(new Audit([
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]));
    }

	public function auditCreating($event = 'create')
	{
        $userID = Auth::id();
        if (is_null($userID)) {
            throw new \RuntimeException("Auth::id() is null");
        }
		$oldNew = $this->getCreatedEventAttributes();
		return $this->audits()->save(new Audit([
            'user_id' => $userID,
            'event' => $event,
            'old_values' => $oldNew[0],
            'new_values' => $oldNew[1],
        ]));
	}

	public function auditUpdating($event = 'update')
	{
        $userID = Auth::id();
        if (is_null($userID)) {
            throw new \RuntimeException("Auth::id() is null");
        }
        $oldNew = $this->getUpdatedEventAttributes();
        return $this->audits()->save(new Audit([
            'user_id' => $userID,
            'event' => $event,
            'old_values' => $oldNew[0],
            'new_values' => $oldNew[1],
        ]));
	}

    public function auditDeleting($event = 'delete')
    {
        $userID = Auth::id();
        if (is_null($userID)) {
            throw new \RuntimeException("Auth::id() is null");
        }
        $oldNew = $this->getDeletedEventAttributes();
        return $this->audits()->save(new Audit([
            'user_id' => $userID,
            'event' => $event,
            'old_values' => $oldNew[0],
            'new_values' => $oldNew[1],
        ]));
    }

	public function audits()
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

	/**
     * Determine if an attribute is eligible for auditing.
     *
     * @param string $attribute
     *
     * @return bool
     */
    protected function isAttributeAuditable(string $attribute): bool
    {
        // The attribute should not be audited
        if (in_array($attribute, $this->auditExcludedAttributes, true)) {
            return false;
        }

        // The attribute is auditable when explicitly
        // listed or when the include array is empty
        return empty($this->auditIncludedAttributes) || in_array($attribute, $this->auditIncludedAttributes, true);
    }


	/**
     * Get the old/new attributes of a created event.
     *
     * @return array
     */
    protected function getCreatedEventAttributes(): array
    {
        $new = [];

        foreach ($this->attributes as $attribute => $value) {
            if ($this->isAttributeAuditable($attribute)) {
                $new[$attribute] = $value;
            }
        }

        return [
            [],
            $new,
        ];
    }

	/**
     * Get the old/new attributes of an updated event.
     *
     * @return array
     */
    protected function getUpdatedEventAttributes(): array
    {
        $old = [];
        $new = [];

        foreach ($this->getDirty() as $attribute => $value) {
            if ($this->isAttributeAuditable($attribute)) {
                $old[$attribute] = Arr::get($this->original, $attribute);
                $new[$attribute] = Arr::get($this->attributes, $attribute);
            }
        }

        return [
            $old,
            $new,
        ];
    }

    /**
     * Get the old/new attributes of a deleted event.
     *
     * @return array
     */
    protected function getDeletedEventAttributes(): array
    {
        $old = [];

        foreach ($this->attributes as $attribute => $value) {
            if ($this->isAttributeAuditable($attribute)) {
                $old[$attribute] = $value;
            }
        }

        return [
            $old,
            [],
        ];
    }
}
