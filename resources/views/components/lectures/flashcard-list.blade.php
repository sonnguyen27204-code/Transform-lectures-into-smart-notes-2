@props(['lecture'])

@if($lecture->flashcards->count())
    <x-ui.card title="Flashcards" :description="$lecture->flashcards->count() . ' thẻ ghi nhớ'">
        <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2">
            @foreach($lecture->flashcards as $card)
                <x-lectures.flashcard :card="$card" />
            @endforeach
        </div>
        <p class="text-xs text-ink-500 text-center mt-3">Click vào thẻ để lật</p>
    </x-ui.card>
@endif