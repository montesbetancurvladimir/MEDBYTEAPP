<!DOCTYPE html>
<html lang="en">
<head>
    <title>Medbyte</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="{{ asset('css/styles_home.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/animate.css@4.1.1/animate.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" data-tag="font" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=STIX+Two+Text:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" data-tag="font" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" data-tag="font" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" data-tag="font" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" data-tag="font" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="logo-container">
        <a href="{{ route('home') }}">
            <img
                src="{{ asset('external/logo31111500-fd8m-200h.png') }}"
                alt="LOGO31111500"
                class="landing-logo3111"
            />
        </a>
    </div>
    <div>
        <div class="landing-container">
            <div class="landing-landing">
                <div class="landing-menu-items"></div>
                <span class="landing_text_question">
                    <span> {{ $question->text }}</span>
                    <br />
                </span><br>
                @if ($question->type == 'multiple_choice')
                    <!-- Pregunta de selección múltiple -->
                    <form action="{{ route('survey.answer_cache') }}" method="POST">
                        @csrf
                        <input type="hidden" name="question_id" value="{{ $question->id }}">
                        <div class="custom-combobox">
                            <div class="styled-options">
                                @foreach ($question->options as $option)
                                    <span class="styled-option" data-value="{{ $option->id }}" onclick="handleOptionClick(this)">{{ $option->text }}</span>
                                @endforeach
                            </div>
                            <input type="hidden" id="selectedOption" name="selected_option" value="">
                        </div>
                        <script>
                            function handleOptionClick(option) {
                                const options = document.querySelectorAll('.styled-option');
                                options.forEach(opt => opt.classList.remove('active'));
                                option.classList.add('active');
                                const selectedOption = option.getAttribute('data-value');
                                console.log('Selected option:', selectedOption); // Verifica el valor en la consola
                                document.getElementById('selectedOption').value = selectedOption;
                            }
                        </script><br><br>
                        <button class="btn btn-primary landing-text13" type="submit">Siguiente</button>
                    </form>
                @elseif ($question->type == 'open')
                    <!-- Pregunta abierta -->
                    <form action="{{ route('survey.answer_cache') }}" method="POST">
                        @csrf
                        <input type="hidden" name="question_id" value="{{ $question->id }}">
                        <input type="text" name="response" style="background-color: #F2F4EA; border: 2px solid #5C937B; padding: 10px;">
                        <br><br>
                        <button class="btn btn-primary landing-text13" type="submit">Siguiente</button>
                    </form>
                @elseif ($question->type == 'multiple_options')
                    <!-- Pregunta de selección múltiple PERO con multiples respuestas -->
                    <form action="{{ route('survey.answer_cache') }}" method="POST">
                        @csrf
                        {{-- Subtítulo --}}
                        <input type="hidden" name="question_id" value="{{ $question->id }}">
                        <div class="custom-combobox">
                            <div class="styled-options">
                                @foreach ($question->options as $option)
                                    <span class="styled-option" data-value="{{ $option->id }}" onclick="handleOptionClick(this)">{{ $option->text }}</span>
                                @endforeach
                            </div>
                            <input type="hidden" id="selectedOptions" name="selected_options" value="">
                        </div>
                        <script>
                            function handleOptionClick(option) {
                                option.classList.toggle('active');
                                const selectedOptions = [];
                                const options = document.querySelectorAll('.styled-option.active');
                                options.forEach(opt => {
                                    selectedOptions.push(opt.getAttribute('data-value'));
                                });
                                console.log('Selected options:', selectedOptions); // Verifica los valores en la consola
                                document.getElementById('selectedOptions').value = selectedOptions.join(',');
                            }
                        </script>
                        <br><br>
                        <button class="btn btn-primary landing-text13" type="submit">Siguiente</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

</body>
</html>
