@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.book.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.books.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.book.fields.code') }}
                        </th>
                        <td>
                            {{ $book->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.book.fields.name') }}
                        </th>
                        <td>
                            {{ $book->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.book.fields.description') }}
                        </th>
                        <td>
                            {{ $book->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.book.fields.jenjang') }}
                        </th>
                        <td>
                            {{ $book->jenjang->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.book.fields.kurikulum') }}
                        </th>
                        <td>
                            {{ $book->kurikulum->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.book.fields.mapel') }}
                        </th>
                        <td>
                            {{ $book->mapel->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.book.fields.kelas') }}
                        </th>
                        <td>
                            {{ $book->kelas->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.book.fields.cover') }}
                        </th>
                        <td>
                            {{ $book->cover->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.book.fields.semester') }}
                        </th>
                        <td>
                            {{ $book->semester->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.book.fields.photo') }}
                        </th>
                        <td>
                            @foreach($book->photo as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $media->getUrl('thumb') }}">
                                </a>
                            @endforeach
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.books.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection