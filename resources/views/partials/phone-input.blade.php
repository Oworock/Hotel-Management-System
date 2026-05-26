@php
    $field = $field ?? 'phone';
    $countryField = $countryField ?? $field . '_country_code';
    $label = $label ?? 'Phone Number';
    $required = $required ?? false;
    [$selectedCountryCode, $nationalNumber] = \App\Support\PhoneNumber::split(old($field, $value ?? ''), $defaultCountryCode ?? '+234');
    $selectedCountryCode = old($countryField, $selectedCountryCode);
@endphp

<div class="form-group" style="{{ $style ?? '' }}">
    <label class="form-label">{{ $label }}</label>
    <div style="display:grid;grid-template-columns:minmax(130px, 0.55fr) minmax(0, 1fr);gap:0.65rem;">
        <select name="{{ $countryField }}" class="form-control form-select" {{ $required ? 'required' : '' }}>
            @foreach(\App\Support\PhoneNumber::countries() as $code => $country)
                <option value="{{ $code }}" {{ $selectedCountryCode === $code ? 'selected' : '' }}>{{ \App\Support\PhoneNumber::countrySelectLabel($code, $country) }}</option>
            @endforeach
        </select>
        <input type="tel" name="{{ $field }}" class="form-control" value="{{ old($field, $nationalNumber) }}" placeholder="8012345678" inputmode="tel" autocomplete="tel-national" {{ $required ? 'required' : '' }}>
    </div>
    <small style="display:block;margin-top:0.35rem;color:var(--text-secondary);font-size:0.75rem;">Enter the local mobile number only. A leading 0 will be removed automatically.</small>
</div>
