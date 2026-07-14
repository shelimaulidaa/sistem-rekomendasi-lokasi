from html.parser import HTMLParser

class MyHTMLParser(HTMLParser):
    def __init__(self):
        super().__init__()
        self.stack = []
        self.line_offset = 279 

    def handle_starttag(self, tag, attrs):
        if tag == 'div':
            self.stack.append((self.getpos()[0] + self.line_offset, attrs))

    def handle_endtag(self, tag):
        if tag == 'div':
            if self.stack:
                self.stack.pop()

    def close(self):
        super().close()
        for pos in self.stack:
            print(f"Unclosed div starting at line {pos[0]}: {pos[1]}")

with open('step1.html', 'r') as f:
    parser = MyHTMLParser()
    parser.feed(f.read())
    parser.close()
