<div id = "dealsbar-deals-toolbar" style = " 
            background-color: !!toolbar-bg-color!!;
            color: !!toolbar-text-color!!;
            height: !!toolbar-pixels!!;      
            font-size: !!font-size!!;  
            !!toolbar-position!!: 0;
            !!toolbar-custom-css!!
          "> 
          !!toolbar-logo!! 
  <span id = "dealsbar-deal" style = "display: table-cell; vertical-align: middle; line-height: 1.25;">
  <span id = "dealsbar-deal-title">!!toolbar-text!!</span>  
    <a id = "dealsbar-deal-text" style = "color: inherit !important; text-decoration: underline !important;" href = " $random_deal->trackingurl . '" target = "_blank">
      !!toolbar-merchant-deal!! - !!toolbar-merchant!!
    </a>
  </span>
  <div id = "dealsbar-toolbar-navi" style = "float:right; margin-right: 5px; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; white-space: nowrap; overflow: hidden;">
    <i id = "dealsbar-toolbar-left" style = "cursor: pointer;" title = "Previous Deal" class = "fa fa-chevron-circle-left"></i>
    <i id = "dealsbar-toolbar-right" style = "cursor: pointer;" title = "Next Deal" class = "fa fa-chevron-circle-right"></i>
    <i id = "dealsbar-toolbar-close" style = "cursor: pointer;" title = "Close Deals" class = "fa fa-times-circle"></i>
  </div>
</div>