@extends('layout.app')

@section('title', isset($game) ? 'Изменение' : 'Главная страница')

@section('content')
  <div class="row">
    <div class="col-md-6">
      <form action="{{ isset($game) ? route('games.update', $game->id) : route('games.store') }}" method="POST">
        @isset($game)
          @method('PATCH')
        @endisset
        @csrf
        <div class="container">
          <div class="row">
            <div class="col">
              <div class="controls">
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label for="name">Название</label>
                      <input type="text" class="@error('name') is-invalid @enderror form-control" id="name"
                        name="name" value="{{ old('name', isset($game) ? $game->name : '') }}">
                      @error('name')
                        <div class="invalid-feedback">
                          {{ $message }}
                        </div>
                      @enderror
                    </div>
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label for="year_of_issue">Год производства</label>
                      <input type="namber" class="@error('year_of_issue') is-invalid @enderror form-control"
                        id="year_of_issue" name="year_of_issue"
                        value="{{ old('year_of_issue', isset($game) ? $game->year_of_issue : '') }}">
                      @error('year_of_issue')
                        <div class="invalid-feedback">
                          {{ $message }}
                        </div>
                      @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label>Страна производства </label>
                      <select class="form-control" name="country_id">
                        @foreach ($countries as $country)
                          <option
                            @isset($game)
                                @selected($game->country_id === $country->id)
                            @endisset
                            value="{{ $country->id }}">
                            {{ $country->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label for="duration">Продолжительность в минутах</label>
                      <input type="namber" class="@error('duration') is-invalid @enderror form-control" id="duration"
                        name="duration" value="{{ old('duration', isset($game) ? $game->duration : '') }}">
                      @error('duration')
                        <div class="invalid-feedback">
                          {{ $message }}
                        </div>
                      @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label for="age">Возрастное ограничение</label>
                      <input type="namber" class="@error('age') is-invalid @enderror form-control" id="age"
                        name="age" value="{{ old('age', isset($game) ? $game->age : '') }}">
                      @error('age')
                        <div class="invalid-feedback">
                          {{ $message }}
                        </div>
                      @enderror
                    </div>
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label for="link_img">Картинка игры</label>
                      <div class="input-group">
                        <input type="text" class="@error('link_img') is-invalid @enderror form-control" id="link_img"
                          name="link_img" value="{{ old('link_img', isset($game) ? $game->link_img : '') }}">
                      </div>
                      @error('link_imge')
                        <div class="invalid-feedback">
                          {{ $message }}
                        </div>
                      @enderror
                    </div>
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script>
                      $(document).ready(function() {
                        function showPreview() {
                          var imageUrl = $('#link_img').val();
                          $('#poster-preview').attr('src', imageUrl).toggle(imageUrl !== '');
                        }
                        showPreview();
                        $('#link_img').on('input', function() {
                          showPreview();
                        });
                      });
                    </script>
                    @error('link_imge')
                      <div class="invalid-feedback">
                        {{ $message }}
                      </div>
                    @enderror
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label for="link_kinopoisk">Ссылка на кинопоиск</label>
                      <input type="text" class="@error('link_kinopoisk') is-invalid @enderror form-control"
                        id="link_kinopoisk" name="link_kinopoisk"
                        value="{{ old('link_kinopoisk', isset($game) ? $game->link_kinopoisk : '') }}">
                      @error('link_kinopoisk')
                        <div class="invalid-feedback">
                          {{ $message }}
                        </div>
                      @enderror
                    </div>
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label for="link_video">Ссылка на трейлер</label>
                      <input type="text" class="@error('link_video') is-invalid @enderror form-control" id="link_video"
                        name="link_video" value="{{ old('link_video', isset($game) ? $game->link_video : '') }}">
                      @error('link_video')
                        <div class="invalid-feedback">
                          {{ $message }}
                        </div>
                      @enderror
                    </div>
                  </div>
                  <div class="col">
                    <button type="submit" class="btn btn-success button-save mt-3"
                      style="margin-left: 5px;">Сохранить</button>
                    <div class="my-4">
                      <img src="{{ old('link_img', isset($game) ? $game->link_img : '') }}" alt="Постер"
                        class="img-game-createblade" id="poster-preview">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
@endsection
