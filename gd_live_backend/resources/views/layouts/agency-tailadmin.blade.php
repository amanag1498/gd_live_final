@php
  $isAdminPreview = request()->routeIs('admin.*');
  $menuContext = $isAdminPreview ? 'admin' : 'agency';
  $homeRoute = $isAdminPreview ? route('admin.dashboard') : route('agency.dashboard');
  $panelLabel = $isAdminPreview ? 'Admin Panel' : 'Agency Panel';
  $defaultTitle = $isAdminPreview ? 'GD Live Admin' : 'GD Live Agency';
  $roleLabel = $isAdminPreview ? 'Administrator' : 'Agency';
@endphp
@extends('layouts.tailadmin-app')
