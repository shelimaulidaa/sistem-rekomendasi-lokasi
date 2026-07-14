from html.parser import HTMLParser

class MyHTMLParser(HTMLParser):
    def __init__(self):
        super().__init__()
        self.stack = []
        self.inside = False
        self.depth = 0

    def handle_starttag(self, tag, attrs):
        if attrs and ('class', 'bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl') in attrs:
            self.inside = True
            
        if self.inside and tag == 'div':
            self.depth += 1
            print(f"OPEN: {self.getpos()} depth={self.depth} attrs={attrs}")

    def handle_endtag(self, tag):
        if self.inside and tag == 'div':
            print(f"CLOSE: {self.getpos()} depth={self.depth}")
            self.depth -= 1
            if self.depth == 0:
                self.inside = False

with open('step1.html', 'r') as f:
    lines = f.readlines()[240:] # roughly start of Section 6
    text = "".join(lines)
    parser = MyHTMLParser()
    parser.feed(text)
    parser.close()
