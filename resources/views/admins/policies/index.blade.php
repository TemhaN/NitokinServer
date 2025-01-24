@extends('layout.app')

@section('title', 'Политики')

@section('content')

  <div class="row">
    <div class="col-12">
      <div class="mb-3">
        <a href="{{ route('policies.create') }}" class="btn btn-success button-savet">
          Добавить документ
        </a>
      </div>
      <div class="card">
        <div class="card-header border-0 m-2">
          <h3 class="card-title">Список документов</h3>

          <div class="card-tools">
            <div class="container-input">
              <input id="search" type="text" placeholder="Поиск" name="text" class="input" onkeyup="search()">
              <svg fill="#000000" width="20px" height="20px" viewBox="0 0 1920 1920"
                xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M790.588 1468.235c-373.722 0-677.647-303.924-677.647-677.647 0-373.722 303.925-677.647 677.647-677.647 373.723 0 677.647 303.925 677.647 677.647 0 373.723-303.924 677.647-677.647 677.647Zm596.781-160.715c120.396-138.692 193.807-319.285 193.807-516.932C1581.176 354.748 1226.428 0 790.588 0S0 354.748 0 790.588s354.748 790.588 790.588 790.588c197.647 0 378.24-73.411 516.932-193.807l516.028 516.142 79.963-79.963-516.142-516.028Z"
                  fill-rule="evenodd"></path>
              </svg>
            </div>
          </div>
        </div>
        <div class="card-body table-responsive p-0">
          @if ($policies->isEmpty())
            <p class="text-center">Политики отсутствуют.</p>
          @else
            <table class="table table-hover text-nowrap table-borderless table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Тип</th>
                  <th>Дата вступления</th>
                  <th style="width: 5%">Действия</th>
                </tr>
              </thead>
              <tbody class="categories-list">
                @foreach ($policies as $policy)
                  <tr>
                    <th>
                      <a href="#" data-toggle="modal"
                        data-target="#editPolicyModal{{ $policy->id }}">{{ $policy->id }}</a>
                      <div class="modal fade" id="editPolicyModal{{ $policy->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="editPolicyModalLabel{{ $policy->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="editPolicyModalLabel{{ $policy->id }}">Редактировать документ
                              </h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" style="font-size: 2.5rem; color: #333333;">×</span>
                              </button>
                            </div>
                            <form action="{{ route('policies.update', $policy->id) }}" method="POST">
                              @csrf
                              @method('PATCH')
                              <div class="modal-body">
                                <div class="form-group">
                                  <label for="name">Тип</label>
                                  <input type="text" class="@error('type') is-invalid @enderror form-control"
                                    id="type" name="type"
                                    value="{{ old('type', isset($policy) ? $policy->type : '') }}">
                                  @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                  @enderror
                                </div>
                                <div class="form-group">
                                  <label for="effective_date">Дата вступления</label>
                                  <input type="date" class="@error('effective_date') is-invalid @enderror form-control"
                                    id="effective_date" name="effective_date"
                                    value="{{ old('effective_date', isset($policy) ? $policy->effective_date : '') }}">
                                  @error('effective_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                  @enderror
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" class="btn btn-success button-save">Сохранить</button>
                                <button type="button" class="btn btn-danger button-close"
                                  data-dismiss="modal">Закрыть</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </th>
                    <td>{{ $policy->type === 'terms' ? 'Пользовательское соглашение' : 'Политика конфиденциальности' }}
                    </td>
                    <td>{{ $policy->effective_date }}</td>
                    <td>
                      <form action="{{ route('policies.destroy', $policy->id) }}" method="POST"
                        style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger button-close">Удалить</button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>
    </div>
  </div>

@endsection
