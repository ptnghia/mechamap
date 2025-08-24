<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing conversations to new structure
        $this->migrateExistingConversations();
        
        // Create conversation participants as group members for existing conversations
        $this->migrateConversationParticipants();
        
        // Update conversation metadata
        $this->updateConversationMetadata();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset conversations to original state
        DB::table('conversations')->update([
            'conversation_type_id' => null,
            'is_group' => false,
            'group_request_id' => null,
            'max_members' => 2,
            'is_public' => false,
            'group_description' => null,
            'group_rules' => null,
        ]);
        
        // Remove group members data
        DB::table('group_members')->truncate();
    }

    private function migrateExistingConversations(): void
    {
        // Update all existing conversations to be private 1-on-1 conversations
        DB::table('conversations')
            ->whereNull('conversation_type_id')
            ->update([
                'is_group' => false,
                'max_members' => 2,
                'is_public' => false,
            ]);
            
        echo "✅ Migrated " . DB::table('conversations')->count() . " conversations\n";
    }

    private function migrateConversationParticipants(): void
    {
        $conversations = DB::table('conversations')->get();
        $migratedCount = 0;
        
        foreach ($conversations as $conversation) {
            // Get participants for this conversation
            $participants = DB::table('conversation_participants')
                ->where('conversation_id', $conversation->id)
                ->get();
            
            foreach ($participants as $participant) {
                // Create group member record for each participant
                // First participant becomes creator, others become members
                $isCreator = $participants->first()->user_id === $participant->user_id;
                
                DB::table('group_members')->insertOrIgnore([
                    'conversation_id' => $conversation->id,
                    'user_id' => $participant->user_id,
                    'role' => $isCreator ? 'creator' : 'member',
                    'joined_at' => $conversation->created_at,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $migratedCount++;
            }
        }
        
        echo "✅ Migrated {$migratedCount} conversation participants to group members\n";
    }

    private function updateConversationMetadata(): void
    {
        // Update conversations with proper titles if empty
        $conversationsWithoutTitle = DB::table('conversations')
            ->where(function($query) {
                $query->whereNull('title')
                      ->orWhere('title', '')
                      ->orWhere('title', ' ');
            })
            ->get();
            
        foreach ($conversationsWithoutTitle as $conversation) {
            // Get participants names
            $participants = DB::table('conversation_participants')
                ->join('users', 'conversation_participants.user_id', '=', 'users.id')
                ->where('conversation_participants.conversation_id', $conversation->id)
                ->pluck('users.name')
                ->toArray();
                
            if (count($participants) >= 2) {
                $title = "Cuộc trò chuyện giữa " . implode(' và ', $participants);
                
                DB::table('conversations')
                    ->where('id', $conversation->id)
                    ->update(['title' => $title]);
            }
        }
        
        echo "✅ Updated conversation titles\n";
    }
};
