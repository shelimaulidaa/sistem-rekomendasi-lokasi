from html.parser import HTMLParser

class MyHTMLParser(HTMLParser):
    def __init__(self):
        super().__init__()
        self.depth = 0
        self.stack = []
        self.line_offset = 279

    def handle_starttag(self, tag, attrs):
        if tag == 'div':
            self.depth += 1
            self.stack.append((self.getpos()[0] + self.line_offset, self.depth))

    def handle_endtag(self, tag):
        if tag == 'div':
            if self.stack:
                closed = self.stack.pop()
            self.depth -= 1

    def close(self):
        super().close()
        for item in self.stack:
            print(f"UNCLOSED: {item}")

with open('step1.html', 'r') as f:
    parser = MyHTMLParser()
    parser.feed(f.read())
    parser.close()
