@props(['lecture'])

@if($lecture->quizzes->count())
    <x-ui.card title="Quiz ôn tập" :description="$lecture->quizzes->count() . ' câu hỏi trắc nghiệm'">
        <div class="space-y-4">
            @foreach($lecture->quizzes as $i => $quiz)
                <x-lectures.quiz-item :quiz="$quiz" :index="$i + 1" />
            @endforeach
        </div>
    </x-ui.card>
@endif