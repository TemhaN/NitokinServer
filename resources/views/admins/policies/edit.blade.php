@extends('layout.app')

@section('title', 'Политики')

@section('content')
  <div class="container">
    <h1>Редактировать документ</h1>
    <form action="{{ route('policies.update', $policy->id) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="mb-3">
        <label for="type" class="form-label">Тип документа</label>
        <select name="type" id="type" class="form-select">
          <option value="terms" {{ $policy->type === 'terms' ? 'selected' : '' }}>Пользовательское соглашение</option>
          <option value="privacy" {{ $policy->type === 'privacy' ? 'selected' : '' }}>Политика конфиденциальности</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="content" class="form-label">Содержание</label>
        <textarea name="content" id="content" class="form-control" rows="10">{{ $policy->content }}</textarea>
      </div>
      <div class="mb-3">
        <label for="effective_date" class="form-label">Дата вступления в силу</label>
        <input type="date" name="effective_date" id="effective_date" class="form-control"
          value="{{ $policy->effective_date }}">
      </div>
      <button type="submit" class="btn btn-primary">Обновить</button>
    </form>
  </div>
@endsection
