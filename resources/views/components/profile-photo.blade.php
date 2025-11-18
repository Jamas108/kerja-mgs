@props(['user', 'size' => 40, 'class' => ''])

@if($user->photo)
    <img src="{{ asset('storage/' . $user->photo) }}"
         alt="{{ $user->name }}"
         class="rounded-circle {{ $class }}"
         style="width: {{ $size }}px; height: {{ $size }}px; object-fit: cover;">
@else
    <div class="rounded-circle d-inline-flex align-items-center justify-content-center {{ $class }}"
         style="width: {{ $size }}px;
                height: {{ $size }}px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                font-weight: 600;
                font-size: {{ $size * 0.4 }}px;">
        {{ strtoupper(substr($user->name, 0, 1)) }}
    </div>
@endif