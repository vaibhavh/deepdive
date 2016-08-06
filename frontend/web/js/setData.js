//
//var width = totalNodes > 50 ? 2100 : 1480;
//var height = totalNodes > 50 ? 1800 : 1000;
var mainView;
//var newArr = [];
//    $.each(links, function(i,data){
//        var is_pair_exist = false;
//        $.each(newArr, function(j, data2){
//            if(data2.source == data.target && data2.target == data.source)
//                is_pair_exist = true;
//        })
//        if(is_pair_exist === false){
//            newArr.push({"source":data.source,"target":data.target});
//        }
//    });
// console.log(newArr);
// var topologyData = {
//        nodes: nodes,
//        links: newArr
//};
console.log(topologyData);
 
nx.define('MyTopology', nx.ui.Component, {
    properties: {
        node: {},
        topology: {}
    },
    view: {
        content: [
            {
//                type: 'search.device.ActionPanel'
//            }, {
                name: 'topo',
                type: 'nx.graphic.Topology',
                props: {
                     adaptive: true,
                    identityKey: 'id',
                    autoLayout: true,
//                    dataProcessor: 'force',
                    width: 1000,
                    height: 500,
                    nodeConfig: {
                        iconType: function(vertex) {
//                            return vertex.get('iconType');
                            return 'nexus';
                        },
                        label: function(vertex) {
                            return vertex.get('hostname');
                        },
                        
                        color: function(vertex){
                            return vertex.get('color');
                        }
                    },
                    linkConfig: {
                        // multiple link type is curve, could change to 'parallel' to use parallel link
                        linkType: 'curve',
                        color: function(link, model) {
                            return link.getData()['link_color'];
//                            return colorTable[Math.floor(Math.random() * 5)];
                        },
                        width: 2.5

                    },
//                    tooltipManagerConfig: {
//                        nodeTooltipContentClass: nodeToolTip
//                    },
                    showIcon: true,
                    data: topologyData,
                },
//                events: {
//                    topologyGenerated: '{#custom_loader}',
//                    //selectNode :'{#showNodesForClocking}',
//                    clickStage: '{#onClick}'
//                }
            }]
    }
});

var App = nx.define(nx.ui.Application, {
    methods: {
        getContainer: function() {
            return new nx.dom.Element(document.getElementById('clocking_delta_topology'));
        },
        start: function() {
            mainView = new MyTopology();
            mainView.attach(this);
        }
    }
});

var app = new App();
$('div.custom-loader').show();
app.start();