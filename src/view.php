<div class="apidoc-page">

    {{run $counter = 0}}
    <div class="side">

        {{foreach $tree as $section => $endpoints}}

            {{if $section != '__no_section__'}}
                <h4>{{$section}}</h4>
            {{/}}

            {{foreach $endpoints as $endpoint}}

                {{foreach $endpoint['methods'] as $method => $data}}

                    {{run $counter += 1}}
                    <div class="row endpoint" endpointId="e{{$counter}}">
                        <div class="method-label {{$method}}">{{$method}}</div>
                        <div class="endpoint-name">{{ $data['name'] ?? $data['path']}}</div>
                    </div>

                {{/}}

            {{/}}

        {{/}}

    </div>

    <div  class="content">

    <?php
    $whereDesc = function($method, $path, $name, $param) {
        $where = in_array($method, ['GET', 'DELETE']) ? 'query' : 'data';

        if (isset($param['where']) && in_array($param['where'], ['query', 'data', 'url'])) {
            $where = $param['where'];
        }

        if ($where == 'query') {
            return "<b>Query</b>: $path/?$name=xxx";
        }
        if ($where == 'url') {
            return "<b>URL</b>: url/:$name";
        }
        return "<b>DATA</b>: { $name: 'xxx' }";
    };

    $colorResponse = function($code) {
        if ($code >= 200 && $code < 300) {
            return 'rgb(102, 212, 80)';
        }
        if ($code >= 400) {
            return 'red';
        }
        return 'orange';
    };
    ?>

    {{run $counter = 0}}
    {{foreach $tree as $section => $endpoints}}

        {{if $section != '__no_section__'}}
            <h4>{{$section}}</h4>
        {{/}}

        {{foreach $endpoints as $endpoint}}

            {{foreach $endpoint['methods'] as $method => $data}}
            {{run $counter += 1}}
            <div class="endpoint-data" id="e{{$counter}}">

                <div class="row endpoint">
                    <div class="method-label {{$method}}">{{$method}}</div>
                    <div>{{$data['path']}}</div>
                </div>

                <p>{{ $data['description'] ?? ( $data['name'] ?? '' ) }}</p>

                {{if isset($data['parameters'])}}
                <div class="table">
                    <div class="table-row">
                        <div class="table-col top-col">Parameter</div>
                        <div class="table-col large-col top-col">Description</div>
                        <div class="table-col top-col">Type</div>
                        <div class="table-col top-col">Where</div>
                        <div class="table-col top-col">Possible values</div>
                    </div>

                    {{foreach $data['parameters'] as $name => $param}}
                    <div class="table-row">
                        <div class="table-col">{{$name}}</div>
                        <div class="table-col large-col">{{ $param['description'] ?? '' }}</div>
                        <div class="table-col">{{ $param['type'] ?? 'mixed' }}</div>
                        <div class="table-col">{{ $whereDesc($method, $data['path'], $name, $param) }}</div>
                        <div class="table-col">{{ $param['values'] ?? '' }}</div>
                    </div>
                    {{/}}
                </div>
                {{/}}

                {{if isset($data['response'])}}
                <p>Response:</p>

                    {{foreach $data['response'] as $code => $response }}
                        <p><b style="color: {{ $colorResponse(intval($code)) }}">{{$code}}</b>: {{ $response['description'] ?? ''}}</p>
                        {{if isset($response['example']) }}
                        <div class="response-preview">{{ JSON::stringify($response['example'], true) }}</div>
                        {{/}}
                    {{/}}

                {{/}}

            </div>
            {{/}}

        {{/}}

    {{/}}

    </div>
</div>
<script>
ready(() => {
    let content = $(".apidoc-page > .content");

    $(".apidoc-page .side .endpoint")
    .click(function() {
        let id = $(this).attr('endpointid');
        let e = $('#'+id);
        content.scrollTo(e, {margin: 30});
    });
});
</script>