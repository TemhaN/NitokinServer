@extends('layout.app')

@section('title', 'Политики')

@section('content')
  <div class="container">
    <h1>Добавить документ</h1>
    <form action="{{ route('policies.store') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label for="type" class="form-label">Тип документа</label>
        <select name="type" id="type" class="form-select">
          <option value="terms">Пользовательское соглашение</option>
          <option value="privacy">Политика конфиденциальности</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="content" class="form-label">Содержание</label>
        <textarea name="content" id="content" class="form-control" rows="10"></textarea>
      </div>
      <div class="mb-3">
        <label for="effective_date" class="form-label">Дата вступления в силу</label>
        <input type="date" name="effective_date" id="effective_date" class="form-control">
      </div>
      <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
  </div>
@endsection
