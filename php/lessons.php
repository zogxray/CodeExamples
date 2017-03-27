<?php

Route::group(['namespace' => 'Front\University', 'as' => 'lessons::', 'prefix' => 'lessons'], function () {

    Route::post('/store', ['as' => 'store', 'uses' => 'LessonController@store'])->middleware('school');
    Route::post('/update', ['as' => 'update', 'uses' => 'LessonController@update'])->middleware('school');
    Route::post('/check-status', ['as' => 'checkStatus', 'uses' => 'LessonController@checkStatus'])->middleware('school');
    Route::post('/delete-video', ['as' => 'deleteVideo', 'uses' => 'LessonController@deleteVideo'])->middleware('school');
    Route::post('/delete', ['as' => 'delete', 'uses' => 'LessonController@delete'])->middleware('school');
});