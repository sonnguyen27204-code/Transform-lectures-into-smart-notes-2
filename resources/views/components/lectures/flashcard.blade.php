@props(['card'])

<div class="flashcard group perspective-1000 cursor-pointer h-32" onclick="this.classList.toggle('flipped')">
    <div class="relative preserve-3d w-full h-full transition-transform duration-500 group-[.flipped]:[transform:rotateY(180deg)]">
        <div class="absolute inset-0 backface-hidden bg-gradient-to-br from-brand-500/10 to-accent-500/5 border border-brand-500/20 rounded-xl p-4 flex items-center justify-center text-center">
            <p class="font-display font-semibold text-white">{{ $card->term }}</p>
        </div>
        <div class="absolute inset-0 backface-hidden rotate-y-180 bg-gradient-to-br from-accent-500/10 to-brand-500/5 border border-accent-500/20 rounded-xl p-4 flex items-center justify-center text-center overflow-y-auto">
            <div>
                <p class="text-xs text-accent-300 font-semibold mb-1">{{ $card->term }}</p>
                <p class="text-sm text-ink-200 leading-snug">{{ $card->definition }}</p>
            </div>
        </div>
    </div>
</div>