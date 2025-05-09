@php
    use Illuminate\Support\Str;
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    @if ((isset($users) && count($users) === 1))
        <title>{{ trans('general.assigned_to', ['name' => $users[0]->present()->fullName()]) }} - {{ date('Y-m-d H:i', time()) }}</title>
    @else
        <title>{{ trans('admin/users/general.print_assigned') }} - {{ date('Y-m-d H:i', time()) }}</title>
    @endisset

    <link rel="shortcut icon" type="image/ico" href="{{ ($snipeSettings) && ($snipeSettings->favicon!='') ?  Storage::disk('public')->url(e($snipeSettings->favicon)) : config('app.url').'/favicon.ico' }}">

    <link rel="stylesheet" href="{{ url(mix('css/dist/bootstrap-table.css')) }}">

    {{-- stylesheets --}}
    <link rel="stylesheet" href="{{ url(mix('css/dist/all.css')) }}">

    @php
        // Generate a unique Ref No for this print view
        $refNo = 'FIS/IT/' . strtoupper(Str::random(6)) . '/' . date('Y');
    @endphp

    {{-- Header Image --}}
    <div style="display: flex; flex-direction: row; align-items: flex-start; justify-content: space-between; margin-bottom: 10px;">
        <img class="header-img" src="{{ config('app.url') }}/uploads/FISFAIM.png" style="width: 350px; height: 70px; margin-bottom: 0;">
        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px; margin-left: 20px;">
            <div style="font-size: 1.1em;">
                <strong>Date:</strong>
                <span style="text-decoration: underline;">{{ now()->format('Y-m-d') }}</span>
            </div>
            <div style="font-size: 1.1em;">
                <strong>Ref No.:</strong>
                <span style="display: inline-block; min-width: 180px; border-bottom: 1px solid #333; height: 1.2em; vertical-align: middle;">{{ $refNo }}</span>
            </div>
        </div>
    </div>
    {{-- End Header Image and Info --}}

    {{-- Agreement Section --}}
    <div style="background: #f9f9fc; border-radius: 8px; padding: 24px 28px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
        <h3 style="margin-top: 0;"> <strong>General Device Sign-Off Agreement</strong></h3>
        <p><strong>Purpose:</strong> This agreement outlines the terms and conditions for the use of school-owned devices by authorized personnel.</p>
        <p>It aims to promote responsible use, accountability, and efficient management of these resources while minimizing device damage.</p>
        <h4>Agreement:</h4>
        <p>By signing this agreement, the authorized personnel acknowledge and agree to the following:</p>
        <ul>
            <li><strong>Responsible Use:</strong> The personnel will use the device solely for authorized purposes and in accordance with school policies.</li>
            <li><strong>Careful Usage:</strong> The personnel will handle the device with care, avoiding actions that could cause damage, such as dropping, spilling liquids on, or exposing it to extreme temperatures.</li>
            <li><strong>Maintenance and Care:</strong> The personnel will ensure the device is properly maintained, including regular cleaning and updates, to prevent damage and optimize performance.</li>
            <li><strong>Security:</strong> The personnel will comply with all security measures and policies to protect the device and its data.</li>
            <li><strong>Inventory Management:</strong> The personnel will report any issues or problems with the device promptly to the IT department, including any signs of damage or malfunction.</li>
            <li><strong>Sign-Off Procedure:</strong> When the device is no longer needed or is being transferred, the personnel will sign off on the device in the inventory management system, providing accurate information about its condition and any necessary repairs.</li>
        </ul>
        <p>The specific details of the device, including its model number, serial number, and location, will be recorded and managed in the school's inventory management system as follows.</p>
        <p>By signing this agreement, the authorized personnel also agree to abide by the School IT Policy, and understand that their use of the device will be governed by the rules and guidelines set forth in that policy.</p>
    </div>
    {{-- End Agreement Section --}}

    <script nonce="{{ csrf_token() }}">
        window.snipeit = {
            settings: {
                "per_page": 50
            }
        };
    </script>

    <style>
        body {
            font-family: "Arial, Helvetica", sans-serif;
            padding: 20px;
        }
        table.inventory {
            width: 100%;
            border: 1px solid #d3d3d3;
        }

        @page {
            size: A4;
        }
        
        .print-logo {
            max-height: 40px;
        }

        h4 {
            margin-top: 20px;
            margin-bottom: 10px;
        }

        /* Responsive tables for print */
        @media print {
            .header-img {
                max-width: 350px !important;
                height: 70px !important;
            }
            .agreement-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

{{-- If we are rendering multiple users we'll add the ability to show/hide EULAs for all of them at once via this button --}}
@if (count($users) > 1)
    <div class="pull-right hidden-print">
        <span>{{ trans('general.show_or_hide_eulas') }}</span>
        <button class="btn btn-default" type="button" data-toggle="collapse" data-target=".eula-row" aria-expanded="false" aria-controls="eula-row" title="EULAs">
            <i class="fa fa-eye-slash"></i>
        </button>
    </div>
@endif

@if ($snipeSettings->logo_print_assets=='1')
    @if ($snipeSettings->brand == '3')

        <h2>
            @if ($snipeSettings->logo!='')
                <img class="print-logo" src="{{ config('app.url') }}/uploads/{{ $snipeSettings->logo }}">
            @endif
            {{ $snipeSettings->site_name }}
        </h2>
    @elseif ($snipeSettings->brand == '2')
        @if ($snipeSettings->logo!='')
            <img class="print-logo" src="{{ config('app.url') }}/uploads/{{ $snipeSettings->logo }}">
        @endif
    @else
        <h2>{{ $snipeSettings->site_name }}</h2>
    @endif
@endif

@foreach ($users as $show_user)
    <div id="start_of_user_section"> {{-- used for page breaks when printing --}}</div>
    <h3>
        {{ trans('general.assigned_to', ['name' => $show_user->present()->fullName()]) }}
        {{ ($show_user->employee_num!='') ? ' (#'.$show_user->employee_num.') ' : '' }}
        {{ ($show_user->jobtitle!='' ? ' - '.$show_user->jobtitle : '') }}
    </h3>
    <p></p>{{ trans('admin/users/general.all_assigned_list_generation')}} {{ Helper::getFormattedDateObject(now(), 'datetime', false) }}

    {{-- Add Ref No to notes/description --}}
    <div style="margin-bottom: 10px;">
        <strong>Reference Number:</strong>
        <span style="font-family: monospace;">{{ $refNo }}</span>
    </div>
    {{-- Show notes with appended Agreement Ref --}}
    @php
        $agreementRefLine = "Agreement Ref: No: $refNo";
        $notesWithRef = trim(($show_user->notes ?? '') . "\n" . $agreementRefLine);
    @endphp
    @if(property_exists($show_user, 'notes') || isset($show_user->notes))
        <div style="margin-bottom: 10px;">
            <strong>Notes:</strong>
            <div style="white-space: pre-line;">
                {{ $notesWithRef }}
            </div>
        </div>
    @endif

    @if ($show_user->assets->count() > 0)
        @php
            $counter = 1;
        @endphp

        <div id="assets-toolbar">
            <h4>{{ trans_choice('general.countable.assets', $show_user->assets->count(), ['count' => $show_user->assets->count()]) }}
            </h4>
        </div>

        <table
            class="snipe-table table table-striped inventory"
            id="AssetsAssigned"
            data-pagination="false"
            data-id-table="AssetsAssigned"
            data-search="false"
            data-side-pagination="client"
            data-sortable="true"
            data-toolbar="#assets-toolbar"
            data-show-columns="true"
            data-sort-order="desc"
            data-sort-name="created_at"
            data-show-columns-toggle-all="true"
            data-cookie-id-table="AssetsAssigned">
            <thead>
                <th data-field="asset_id" data-sortable="false" data-visible="true" data-switchable="false">#</th>
                <th data-field="asset_image" data-sortable="true" data-visible="false" data-switchable="true">{{ trans('general.image') }}</th>
                <th data-field="asset_tag" data-sortable="true" data-visible="true" data-switchable="false">{{ trans('admin/hardware/table.asset_tag') }}</th>
                <th data-field="asset_name" data-sortable="true" data-visible="true">{{ trans('general.name') }}</th>
                <th data-field="asset_category" data-sortable="true" data-visible="true">{{ trans('general.category') }}</th>
                <th data-field="asset_model" data-sortable="true" data-visible="true">{{ trans('admin/hardware/form.model') }}</th>
                <th data-field="rtd_location" data-sortable="true" data-visible="true">{{ trans('admin/hardware/form.default_location') }}</th>
                <th data-field="asset_location" data-sortable="true" data-visible="false">{{ trans('general.location') }}</th>
                <th data-field="asset_serial" data-sortable="true" data-visible="true">{{ trans('admin/hardware/form.serial') }}</th>
                <th data-field="asset_checkout_date" data-sortable="true" data-visible="true">{{ trans('admin/hardware/table.checkout_date') }}</th>
                <th data-field="signature" data-sortable="true" data-visible="true">{{ trans('general.signature') }}</th>
            </thead>
            <tbody>
            @foreach ($show_user->assets as $asset)
                @php
                    if (($asset->model->category) && ($asset->model->category->getEula())) $eulas[] = $asset->model->category->getEula()
                @endphp
                <tr>
                    <td>{{ $counter }}</td>
                    <td>
                        @if ($asset->getImageUrl())
                            <img src="{{ $asset->getImageUrl() }}" class="thumbnail" style="max-height: 50px;">
                        @endif
                    </td>
                    <td>{{ $asset->asset_tag }}</td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ (($asset->model) && ($asset->model->category)) ? $asset->model->category->name : trans('general.invalid_category') }}</td>
                    <td>{{ ($asset->model) ? $asset->model->name : trans('general.invalid_model') }}</td>
                    <td>{{ ($asset->defaultLoc) ? $asset->defaultLoc->name : '' }}</td>
                    <td>{{ ($asset->location) ? $asset->location->name : '' }}</td>
                    <td>{{ $asset->serial }}</td>
                    <td>
                        {{ Helper::getFormattedDateObject($asset->last_checkout, 'datetime', false) }}</td>
                    <td>
                        @if (($asset->assetlog->first()) && ($asset->assetlog->first()->accept_signature!=''))
                            <img style="width:auto;height:100px;" src="{{ asset('/') }}display-sig/{{ $asset->assetlog->first()->accept_signature }}">
                        @endif
                    </td>
                </tr>
                @if ($settings->show_assigned_assets)
                    @php
                        $assignedCounter = 1;
                    @endphp
                    @foreach ($asset->assignedAssets as $asset)
                        <tr>
                            <td>{{ $counter }}.{{ $assignedCounter }}</td>
                            <td>
                                @if ($asset->getImageUrl())
                                    <img src="{{ $asset->getImageUrl() }}" class="thumbnail" style="max-height: 50px;">
                                @endif
                            </td>
                            <td>{{ $asset->asset_tag }}</td>
                            <td>{{ $asset->name }}</td>
                            <td>{{ (($asset->model) && ($asset->model->category)) ? $asset->model->category->name : trans('general.invalid_category') }}</td>
                            <td>{{ ($asset->model) ? $asset->model->name : trans('general.invalid_model') }}</td>
                            <td>{{ ($asset->defaultLoc) ? $asset->defaultLoc->name : '' }}</td>
                            <td>{{ ($asset->location) ? $asset->location->name : '' }}</td>
                            <td>{{ $asset->serial }}</td>
                            <td>
                                {{ Helper::getFormattedDateObject($asset->last_checkout, 'datetime', false) }}</td>
                            <td>
                                @if (($asset->assetlog->first()) && ($asset->assetlog->first()->accept_signature!=''))
                                    <img style="width:auto;height:100px;" src="{{ asset('/') }}display-sig/{{ $asset->assetlog->first()->accept_signature }}">
                                @endif
                            </td>
                        </tr>
                        @php
                            $assignedCounter++
                        @endphp
                    @endforeach
                @endif
                @php
                    $counter++
                @endphp
            @endforeach
            </tbody>
        </table>
    @endif

    @if ($show_user->licenses->count() > 0)
        <div id="licenses-toolbar">
            <h4>{{ trans_choice('general.countable.licenses', $show_user->licenses->count(), ['count' => $show_user->licenses->count()]) }}</h4>
        </div>

        <table
            class="snipe-table table table-striped inventory"
            id="licensessAssigned"
            data-toolbar="#licenses-toolbar"
            data-pagination="false"
            data-id-table="licensessAssigned"
            data-search="false"
            data-side-pagination="client"
            data-sortable="true"
            data-show-columns="true"
            data-sort-order="desc"
            data-sort-name="created_at"
            data-show-columns-toggle-all="true"
            data-cookie-id-table="licensessAssigned">
            <thead>
            <tr>
                <th style="width: 20px;" data-sortable="false" data-switchable="false">#</th>
                <th style="width: 40%;" data-sortable="true" data-switchable="false">{{ trans('general.name') }}</th>
                <th style="width: 50%;" data-sortable="true">{{ trans('admin/licenses/form.license_key') }}</th>
                <th style="width: 10%;" data-sortable="true">{{ trans('admin/hardware/table.checkout_date') }}</th>
            </tr>
            </thead>
            @php
                $lcounter = 1;
            @endphp

            @foreach ($show_user->licenses as $license)
                @php
                    if (($license->category) && ($license->category->getEula())) $eulas[] = $license->category->getEula()
                @endphp
                <tr>
                    <td>{{ $lcounter }}</td>
                    <td>{{ $license->name }}</td>
                    <td>
                        @can('viewKeys', $license)
                            {{ $license->serial }}
                        @else
                            <i class="fa-lock" aria-hidden="true"></i> {{ str_repeat('x', 15) }}
                        @endcan
                    </td>
                    <td>{{  $license->pivot->updated_at }}</td>
                </tr>
                @php
                    $lcounter++
                @endphp
            @endforeach
        </table>
    @endif


    @if ($show_user->accessories->count() > 0)
        <div id="accessories-toolbar">
            <h4>{{ trans_choice('general.countable.accessories', $show_user->accessories->count(), ['count' => $show_user->accessories->count()]) }}</h4>
        </div>

        <table
            class="snipe-table table table-striped inventory"
            id="accessoriesAssigned"
            data-toolbar="#accessories-toolbar"
            data-pagination="false"
            data-id-table="accessoriesAssigned"
            data-search="false"
            data-side-pagination="client"
            data-sortable="true"
            data-show-columns="true"
            data-sort-order="desc"
            data-sort-name="created_at"
            data-show-columns-toggle-all="true"
            data-cookie-id-table="accessoriesAssigned">
            <thead>
            <tr>
                <th style="width: 20px;" data-sortable="false" data-switchable="false">#</th>
                <th data-field="accessory_image" data-sortable="true"  data-visible="true">{{ trans('general.image') }}</th>
                <th style="width: 40%;" data-sortable="true" data-switchable="false">{{ trans('general.name') }}</th>
                <th style="width: 50%;" data-sortable="true">{{ trans('general.category') }}</th>
                <th style="width: 10%;" data-sortable="true">{{ trans('admin/hardware/table.checkout_date') }}</th>
                <th style="width: 10%;" data-sortable="true">{{ trans('general.signature') }}</th>
            </tr>
            </thead>
            @php
                $acounter = 1;
            @endphp

            @foreach ($show_user->accessories as $accessory)
                @if ($accessory)
                    @php
                        if (($accessory->category) && ($accessory->category->getEula())) $eulas[] = $accessory->category->getEula()
                    @endphp
                    <tr>
                        <td>{{ $acounter }}</td>
                        <td>
                            @if ($accessory->getImageUrl())
                                <img src="{{ $accessory->getImageUrl() }}" class="thumbnail" style="max-height: 50px;">
                            @endif
                        </td>
                        <td>{{ ($accessory->manufacturer) ? $accessory->manufacturer->name : '' }} {{ $accessory->name }} {{ $accessory->model_number }}</td>
                        <td>{{ $accessory->category->name }}</td>
                        <td>{{ $accessory->pivot->created_at }}</td>

                        <td>
                            @if (($accessory->assetlog->first()) && ($accessory->assetlog->first()->accept_signature!=''))
                                <img style="width:auto;height:100px;" src="{{ asset('/') }}display-sig/{{ $accessory->assetlog->first()->accept_signature }}">
                            @endif
                        </td>
                    </tr>
                    @php
                        $acounter++
                    @endphp
                @endif
            @endforeach
        </table>
    @endif

    @if ($show_user->consumables->count() > 0)
        <div id="consumables-toolbar">
            <h4>{{ trans_choice('general.countable.consumables', $show_user->consumables->count(), ['count' => $show_user->consumables->count()]) }}</h4>
        </div>

        <table
            class="snipe-table table table-striped inventory"
            id="consumablesAssigned"
            data-pagination="false"
            data-toolbar="#consumables-toolbar"
            data-id-table="consumablesAssigned"
            data-search="false"
            data-side-pagination="client"
            data-sortable="true"
            data-show-columns="true"
            data-sort-order="desc"
            data-sort-name="created_at"
            data-show-columns-toggle-all="true"
            data-cookie-id-table="consumablesAssigned">
            <thead>
            <tr>
                <th style="width: 20px;" data-sortable="false" data-switchable="false"></th>
                <th style="width: 40%;" data-sortable="true" data-switchable="false">{{ trans('general.name') }}</th>
                <th style="width: 50%;" data-sortable="true">{{ trans('general.category') }}</th>
                <th style="width: 10%;" data-sortable="true">{{ trans('admin/hardware/table.checkout_date') }}</th>
                <th style="width: 10%;" data-sortable="true">{{ trans('general.signature') }}</th>

            </tr>
            </thead>
            @php
                $ccounter = 1;
            @endphp

            @foreach ($show_user->consumables as $consumable)
                @if ($consumable)
                    @php
                        if (($consumable->category) && ($consumable->category->getEula())) $eulas[] = $consumable->category->getEula()
                    @endphp
                    <tr>
                        <td>{{ $ccounter }}</td>
                        <td>
                        @if ($consumable->deleted_at!='')
                            <td>{{ ($consumable->manufacturer) ? $consumable->manufacturer->name : '' }}  {{ $consumable->name }} {{ $consumable->model_number }}</td>
                            @else
                                {{ ($consumable->manufacturer) ? $consumable->manufacturer->name : '' }}  {{ $consumable->name }} {{ $consumable->model_number }}
                            @endif
                            </td>
                            <td>{{ ($consumable->category) ? $consumable->category->name : ' invalid/deleted category' }} </td>
                            <td>{{  $consumable->pivot->created_at }}</td>
                            <td>
                                @if (($consumable->assetlog->first()) && ($consumable->assetlog->first()->accept_signature!=''))
                                    <img style="width:auto;height:100px;" src="{{ asset('/') }}display-sig/{{ $consumable->assetlog->first()->accept_signature }}">
                                @endif
                            </td>
                    </tr>
                    @php
                        $ccounter++
                    @endphp
                @endif
            @endforeach
        </table>
    @endif

    <h4>Agreement Acceptance:</h4>
    <p>By signing below, the authorized personnel confirms their understanding and acceptance of the terms and conditions outlined in this agreement.</p>

    @php
        if (!empty($eulas)) $eulas = array_unique($eulas);
    @endphp
    {{-- This may have been render at the top of the page if we're rendering more than one user... --}}
    @if (count($users) === 1 && !empty($eulas))
        <p></p>
        <div class="pull-right">
            <button class="btn btn-default hidden-print" type="button" data-toggle="collapse" data-target=".eula-row" aria-expanded="false" aria-controls="eula-row" title="EULAs">
                <i class="fa fa-eye-slash"></i>
            </button>
        </div>
    @endif

    <table style="margin-top: 20px;">
        @if (!empty($eulas))
        <tr class="collapse eula-row">
            <td style="padding-right: 10px; vertical-align: top; font-weight: bold;">EULA</td>
            <td style="padding-right: 10px; vertical-align: top; padding-bottom: 80px;" colspan="3">
                @foreach ($eulas as $key => $eula)
                    {!! $eula !!}
                @endforeach
            </td>
        </tr>
        @endif
        <tr>
            <td style="padding-right: 10px; vertical-align: top; font-weight: bold;">{{ trans('general.signed_off_by') }} :</td>
            <td style="padding-right: 10px; vertical-align: top;">   
          <span style="text-decoration: underline;"> {{ $show_user->present()->fullName() }}
           {{ ($show_user->employee_num!='') ? ' (#'.$show_user->employee_num.') ' : '' }}</span>  </td>
            <td style="padding-right: 10px; vertical-align: top;">___________________</td>
            <td><span style="text-decoration: underline;"> {{ now()->format('Y-m-d') }}</span></td>
        </tr>
        <tr style="height: 70px;">
            <td></td>
            <td style="padding-right: 10px; vertical-align: top;">Name</td>
            <td style="padding-right: 10px; vertical-align: top;">Signature</td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.date') }}</td>
        </tr>
        <tr>
            <td style="padding-right: 10px; vertical-align: top; font-weight: bold;">{{ trans('admin/users/table.manager') }}:</td>
            <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
            <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
            <td>_____________</td>
            <td><span style="text-decoration: underline;"> {{ now()->format('Y-m-d') }}</span></td>
        </tr>
        <tr>
            <td></td>
            <td style="padding-right: 10px; vertical-align: top;">Name</td>
            <td style="padding-right: 10px; vertical-align: top;">Signature</td>
            <td style="padding-right: 10px; vertical-align: top;">{{ trans('general.date') }}</td>
            <td></td>
        </tr>
    </table>

@endforeach

{{-- Javascript files --}}
<script src="{{ url(mix('js/dist/all.js')) }}" nonce="{{ csrf_token() }}"></script>

<script src="{{ url(mix('js/dist/bootstrap-table.js')) }}"></script>
<script src="{{ url(mix('js/dist/bootstrap-table-locale-all.min.js')) }}"></script>

<!-- load english again here, even though it's in the all.js file, because if BS table doesn't have the translation, it otherwise defaults to chinese. See https://bootstrap-table.com/docs/api/table-options/#locale -->
<script src="{{ url(mix('js/dist/bootstrap-table-en-US.min.js')) }}"></script>

<script>
    $('.snipe-table').bootstrapTable('destroy').each(function () {
        console.log('BS table loaded');

        data_export_options = $(this).attr('data-export-options');
        export_options = data_export_options ? JSON.parse(data_export_options) : {};
        export_options['htmlContent'] = false; // this is already the default; but let's be explicit about it
        export_options['jspdf']= {"orientation": "l"};
        // the following callback method is necessary to prevent XSS vulnerabilities
        // (this is taken from Bootstrap Tables's default wrapper around jQuery Table Export)
        export_options['onCellHtmlData'] = function (cell, rowIndex, colIndex, htmlData) {
            if (cell.is('th')) {
                return cell.find('.th-inner').text()
            }
            return htmlData
        }
        $(this).bootstrapTable({
            classes: 'table table-responsive table-no-bordered',
            ajaxOptions: {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            // reorderableColumns: true,
            stickyHeader: true,
            stickyHeaderOffsetLeft: parseInt($('body').css('padding-left'), 10),
            stickyHeaderOffsetRight: parseInt($('body').css('padding-right'), 10),
            undefinedText: '',
            iconsPrefix: 'fa',
            cookieStorage: '{{ config('session.bs_table_storage') }}',
            cookie: true,
            cookieExpire: '2y',
            mobileResponsive: true,
            maintainSelected: true,
            trimOnSearch: false,
            showSearchClearButton: true,
            paginationFirstText: "{{ trans('general.first') }}",
            paginationLastText: "{{ trans('general.last') }}",
            paginationPreText: "{{ trans('general.previous') }}",
            paginationNextText: "{{ trans('general.next') }}",
            pageList: ['10','20', '30','50','100','150','200'{!! ((config('app.max_results') > 200) ? ",'500'" : '') !!}{!! ((config('app.max_results') > 500) ? ",'".config('app.max_results')."'" : '') !!}],
            pageSize: {{  (($snipeSettings->per_page!='') && ($snipeSettings->per_page > 0)) ? $snipeSettings->per_page : 20 }},
            paginationVAlign: 'both',
            queryParams: function (params) {
                var newParams = {};
                for(var i in params) {
                    if(!keyBlocked(i)) { // only send the field if it's not in blockedFields
                        newParams[i] = params[i];
                    }
                }
                return newParams;
            },
            formatLoadingMessage: function () {
                return '<h2><i class="fas fa-spinner fa-spin" aria-hidden="true"></i> {{ trans('general.loading') }} </h4>';
            },
            icons: {
                advancedSearchIcon: 'fas fa-search-plus',
                paginationSwitchDown: 'fa-caret-square-o-down',
                paginationSwitchUp: 'fa-caret-square-o-up',
                fullscreen: 'fa-expand',
                columns: 'fa-columns',
                refresh: 'fas fa-sync-alt',
                export: 'fa-download',
                clearSearch: 'fa-times'
            },
            exportOptions: export_options,

            exportTypes: ['xlsx', 'excel', 'csv', 'pdf','json', 'xml', 'txt', 'sql', 'doc' ],
            onLoadSuccess: function () {
                $('[data-tooltip="true"]').tooltip(); // Needed to attach tooltips after ajax call
            }

        });
    });
</script>

</body>
</html>
