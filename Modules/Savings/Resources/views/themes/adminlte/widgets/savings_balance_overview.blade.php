<div class="grid-stack-item"
     gs-x="{{$config["x"]}}" gs-y="{{$config["y"]}}"
     gs-w="{{$config["width"]}}" gs-h="{{$config["height"]}}" gs-id="SavingsBalanceOverview">
    <div class="grid-stack-item-content">
        {!! $chart->container() !!}
    </div>
</div>

{!! $chart->script() !!}