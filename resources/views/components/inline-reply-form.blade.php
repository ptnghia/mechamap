@props([
    'commentId',
    'threadId',
    'parentUser' => null,
    'parentContent' => null
])

<div class="inline-reply-form mt-3" id="inline-reply-{{ $commentId }}" style="display: none;">
    <div class="card border-0 bg-light">
        <div class="card-body p-3">
            <!-- Reply to info -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-reply me-1"></i>{{ __('thread.replying_to') }}: 
                        <strong class="reply-to-user">{{ $parentUser }}</strong>
                    </small>
                    <button type="button" class="btn btn-sm btn-outline-secondary cancel-inline-reply" 
                            data-comment-id="{{ $commentId }}">
                        <i class="fas fa-times"></i> {{ __('common.cancel') }}
                    </button>
                </div>
                @if($parentContent)
                <div class="mt-1 text-muted small bg-white p-2 rounded border-start border-primary border-3" 
                     style="max-height: 80px; overflow-y: auto;">
                    <div class="reply-to-content">{!! Str::limit(strip_tags($parentContent), 150) !!}</div>
                </div>
                @endif
            </div>

            <!-- Reply form -->
            <form class="inline-reply-form-element" data-comment-id="{{ $commentId }}" data-thread-id="{{ $threadId }}">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $commentId }}">
                
                <!-- Content editor -->
                <div class="mb-3">
                    <x-tinymce-editor
                        name="content"
                        :id="'inline-reply-content-' . $commentId"
                        :value="''"
                        context="reply"
                        :height="150"
                        :placeholder="__('thread.reply_content_placeholder')"
                        :required="true"
                        :compact="true"
                    />
                    <div class="invalid-feedback" style="display: none;"></div>
                </div>

                <!-- Image upload -->
                <div class="mb-3">
                    <x-comment-image-upload
                        :max-files="3"
                        max-size="5MB"
                        context="inline-reply"
                        :upload-text="__('thread.add_images')"
                        :accept-description="__('thread.images_only')"
                        :show-preview="true"
                        :compact="true"
                        :unique-id="'inline-reply-' . $commentId"
                    />
                </div>

                <!-- Submit buttons -->
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>{{ __('thread.reply_will_appear_below') }}
                    </small>
                    <div>
                        <button type="button" class="btn btn-sm btn-secondary cancel-inline-reply me-2" 
                                data-comment-id="{{ $commentId }}">
                            <i class="fas fa-times me-1"></i>{{ __('common.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-sm btn-primary submit-inline-reply">
                            <i class="fas fa-paper-plane me-1"></i>{{ __('thread.send_reply') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
