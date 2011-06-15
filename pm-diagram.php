<?php

require_once('/NOLOH/NOLOH.php');

/* Moving
An example demonstrating Shift and Animate
Difficulty level: Advanced */

class Moving extends WebPage 
{
	// The number of pixels a certain panel will animate
	const Distance = 100;
	// The number of milliseconds it will take each animation to complete
	const Time = 200;
	
	// This is the panel that will be moved around
	private $MovingPanel;
	// A Group with two RadioButtons: Location and Size, which lets the user choose the type of animation to be performed
	private $RadioGroup;
	
	function Moving()
	{
		parent::WebPage('An example using Animate and Shift');
		$this->CSSFiles->Add('http://massdistributionmedia.com/style.css');
		$this->CSSFiles->Add('http://pm.mdm.cc/pm-style.css');
		// Instantiate our moving panel and give it some basic visual properties: a background color and a mouse cursor
		$this->Controls->Add($this->MovingPanel = new Panel(350, 200, 400, 400));
		$this->MovingPanel->CSSClass = 'pm-diagram-bg';
		$this->MovingPanel->Cursor = Cursor::Move;
		/* The following line allows the user to drag the panel around.
		   It is read as "the MovingPanel Shifts its own Location."
		   Whenever a Shift static (rather than Shift::With, which we'll get to later) is added to a Control's Shifts
		   ArrayList, it is always understood that this is shifting while the mouse is held down, 
		   creating a dragging behavior; i.e., it will start when a user's left mouse button is pressed, 
		   and stop when that button is released. */
		$this->MovingPanel->Shifts[] = Shift::Location($this->MovingPanel);
		
		$this->InitMovingPanelChildren();
	}
	
	function InitMovingPanelChildren()
	{
		// Instantiate four arrow images and give them the Hand mouse Cursor
		$left = new Image('Images/left.gif', 10, 180, 40, 40);
		$right = new Image('Images/right.gif', 350, 180, 40, 40);
		$up = new Image('Images/up.gif', 180, 10, 40, 40);
		$down = new Image('Images/down.gif', 180, 350, 40, 40);
		$left->Cursor = $right->Cursor = $up->Cursor = $down->Cursor = Cursor::Hand;
		
		// Give each of the arrows Click ServerEvents, each one of which will correspond to an animation in a certain direction.
		$left->Click = new ServerEvent($this, 'LeftClick');
		$right->Click = new ServerEvent($this, 'RightClick');
		$up->Click = new ServerEvent($this, 'UpClick');
		$down->Click = new ServerEvent($this, 'DownClick');
		
		// Instantiate a Group object holding two RadioButtons, the first of which will start out Checked.
		$this->RadioGroup = new Group();
		$this->RadioGroup->Add($location = new RadioButton('Location', 20, 20, 100));
		$this->RadioGroup->Add($size = new RadioButton('Size', 20, 50, 100));
		$location->Checked = true;
		
		// Add everything
		$this->MovingPanel->Controls->AddRange($left, $right, $up, $down, $this->RadioGroup);
		
		$this->InitShiftWithMovingPanel($left, $right, $up, $down);
	}
	
	function InitShiftWithMovingPanel($left, $right, $up, $down)
	{
		/* The idea behind Shift::With is that an object will be told to move with another object when that object
		   moves, either through dragging or animation. Of course, it is always possible to tell objects simply that they
		   move rather than what they move with and avoid Shift::With entirely, but sometimes Shift::With offers a 
		   greater level of convenience and self-sufficiency. For instance, if the MovingPanel were moved by something
		   from the outside, its children would automatically know how to position themselves, rather than requiring
		   some external object to worry about specific aspects of the MovingPanel. Shift::With was designed 
		   with objected-oriented movements specifically in mind. */
		
		// The right arrow's Left will Shift with the MovingPanel's Width to ensure that it stays right-justified.
		$right->Shifts[] = Shift::With($this->MovingPanel, Shift::Left, Shift::Width);
		// The up and down arrows' Left will also Shift with the MovingPanel's Width, but at HALF the speed, so they stay centered.
		$up->Shifts[]    = Shift::With($this->MovingPanel, Shift::Left, Shift::Width, null, null, 0.5);
		$down->Shifts[]  = Shift::With($this->MovingPanel, Shift::Left, Shift::Width, null, null, 0.5);
		
		// The lfet and right arrows' Top will Shift with the MovingPanel's Height at half the speed, to ensure they stay vertically centered.
		$left->Shifts[]  = Shift::With($this->MovingPanel, Shift::Top, Shift::Height, null, null, 0.5);
		$right->Shifts[] = Shift::With($this->MovingPanel, Shift::Top, Shift::Height, null, null, 0.5);
		// And the down arrow's Top will Shift with the MovingPanel's Height so it stays on the bottom.
		$down->Shifts[]  = Shift::With($this->MovingPanel, Shift::Top, Shift::Height);
	}
	
	function LeftClick()
	{
		/* If the "size" RadioButton is selected, then we need to "resize to the left." Of course, all that means is that we need 
		   to increase the Width, and decrease the Left. So the Width is only animated if Size is selected, but the Left has to
		   be animated in either case.*/
		
		if($this->RadioGroup->SelectedText == 'Size')
			// Animate the Width to its current Width plus a constant, in a constant number of milliseconds
			Animate::Width($this->MovingPanel, $this->MovingPanel->Width + self::Distance, self::Time);
		// Animate the Left to its current Left minus a constant, in a constant number of milliseconds
		Animate::Left($this->MovingPanel, $this->MovingPanel->Left - self::Distance, self::Time);
	}
	
	function RightClick()
	{
		// If the "size" RadioButton is selected, then animate the Width to its current Width plus a constant, in a constant number of milliseconds.
		if($this->RadioGroup->SelectedText == 'Size')
			Animate::Width($this->MovingPanel, $this->MovingPanel->Width + self::Distance, self::Time);
		// Otherwise, animate the Left to its current Left plus a constant, in a constant number of milliseconds.
		else
			Animate::Left($this->MovingPanel, $this->MovingPanel->Left + self::Distance, self::Time);
	}
	
	function UpClick()
	{
		/* If the "size" RadioButton is selected, then we need to "resize up." Of course, all that means is that we need 
		   to increase the Height, and decrease the Top. So the Height is only animated if Size is selected, but the Top has to
		   be animated in either case.*/
		
		if($this->RadioGroup->SelectedText == 'Size')
			// Animate the Height to its current Height plus a constant, in a constant number of milliseconds.
			Animate::Height($this->MovingPanel, $this->MovingPanel->Height + self::Distance, self::Time);
		// Animate the Top to its current Top minus a constant, in a constant number of milliseconds
		Animate::Top($this->MovingPanel, $this->MovingPanel->Top - self::Distance, self::Time);
	}
	
	function DownClick()
	{
		// If the "size" RadioButton is selected, then animate the Height to its current Height plus a constant, in a constant number of milliseconds.
		if($this->RadioGroup->SelectedText == 'Size')
			Animate::Height($this->MovingPanel, $this->MovingPanel->Height + self::Distance, self::Time);
		// Otherwise, animate the Left to its current Left plus a constant, in a constant number of milliseconds
		else
			Animate::Top($this->MovingPanel, $this->MovingPanel->Top + self::Distance, self::Time);
	}
}
?>