<div class="container footbar">
    <hr class="hr">
    V 1.0.0 I
</div>
<style>
    .footbar {
        text-align: center;
        background: none;
        color: rgba(0,0,0,0.1);
    }

    .hr {
        border: 0;
        font-size: 12px;
        padding: 10px 0;
        position: relative;
        background: none;
    }

    .hr::before {
        content: "ZSV";
        position: absolute;
        padding: 0 10px;
        line-height: 1px;
        border: solid #9f9f9f;
        border-width: 0 100vw;
        white-space: nowrap;
        left: 50%;
        transform: translateX(-50%);
    }
</style>