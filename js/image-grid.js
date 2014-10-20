var GridFile = function() {
    this.url        = '';
    this.name       = '';
    this.extension  = '';
    this.thumb      = '';
    this.filesize   = '';

    this.getName = function() {
        return this.name;
    };

    this.getImage = function() {
        return $('<img/>', {
            'class' : 'item',
            'src' : this.thumb
        });
    };

    return this;
};

// console.log(GridFile);
// console.log(new GridFile());


var fileOne = new GridFile();
var fileTwo = new GridFile();


///////////////////////////////////////////////////////////////////////////////////////////////////

var ImageGrid = {};

ImageGrid.results = $('#images');


// Comment

/**
 * This is a document block
 * This will iterate through our files object and display each in an HTML image element
 */


ImageGrid.loadImages = function() {
    // OUr ImageGrid Class
    var self = this;

    if(typeof imageData === 'undefined') { imageData = ImageGridFiles; }

    //    console.log(imageData);

    // for(start; condition; increment variable)
    for(var i = 0; i < imageData.length; i++) {
        var item = imageData[i];

        // console.log(item.file);

        self.results.append(
            $('<img>', {
                'class' : 'item item-'+1,
                'src' : item.thumb_url
            })
        );
    }

    // console.log(item);



};

ImageGrid.loadImages();